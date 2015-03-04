'use strict';

angular
    .module('app')
    .service('ApiService', function($q, $http) {
        return new function() {
            var buckets = {};
            var cache = JSON.parse(localStorage.getItem('cache'));

            if (null === cache) {
                cache = {characters: {}, definitions: {}};
            }

            var saveCache = function() {
                localStorage.setItem('cache', JSON.stringify(cache));
            };

            var cleanBucketItems = function(characterId) {
                angular.forEach(buckets, function(bucket, bucketKey) {
                    angular.forEach(bucket.items, function(item, itemKey) {
                        if (item.characterId == characterId) {
                            delete buckets[bucketKey].items[itemKey];
                        }
                    });
                });
            };

            var cacheDefinitions = function(characterId, definitions) {
                if (this.hasCache(characterId)) {
                    return;
                }

                angular.forEach(definitions, function(definitionData, name) {
                    if (!cache.definitions[name]) {
                        cache.definitions[name] = {};
                    }
                    angular.forEach(definitionData, function(definition, hash) {
                        if (!cache.definitions[name][hash]) {
                            cache.definitions[name][hash] = definition;
                        }

                        if (!cache.characters[characterId]) {
                            cache.characters[characterId] = Date.now();
                        }
                    });
                });

                saveCache();
            }.bind(this);

            var onVaultResponse = function(response) {
                cleanBucketItems();

                angular.forEach(response, function(bucket) {
                    angular.forEach(bucket['items'], function (item) {
                        var bucketHash = cache.definitions.items[item.itemHash].bucketTypeHash;

                        if (!buckets[bucketHash]) {
                            buckets[bucketHash] = {
                                "items": [],
                                "bucketHash": bucketHash
                            };
                        }

                        buckets[bucketHash]['items'].push(item);
                    });
                });
            };

            var onInventoryResponse = function(response, characterId) {
                cleanBucketItems(characterId);

                angular.forEach(response, function(bucketData) {
                    angular.forEach(bucketData, function(bucket) {
                        var bucketHash = bucket.bucketHash;

                        if (!buckets[bucketHash]) {
                            buckets[bucketHash] = {
                                "items": [],
                                "bucketHash": bucketHash
                            };
                        }

                        angular.forEach(bucket['items'], function(item) {
                            item.characterId = characterId;
                        });

                        buckets[bucketHash]['items'] = buckets[bucketHash]['items'].concat(bucket['items']);
                    });
                });
            };

            this.clearCache = function() {
                localStorage.removeItem('cache');
            };

            this.getBuckets = function() {
                return buckets;
            };

            this.getDefinitions = function(type) {
                if (!type) {
                    return cache.definitions;
                }
                return cache.definitions[type] ? cache.definitions[type] : {};
            };

            this.hasCache = function(characterId) {
                return cache.characters[characterId];
            };

            this.getCharacters = function() {
                var d = $q.defer();

                $http
                    .get('/api/characters?definitions=true')
                    .success(function(response) {
                        angular.forEach(response.Response.data.characters, function(character) {
                            var characterId = character.characterBase.characterId;
                            var cacheTimestamp = cache.characters[characterId];
                            var lastPlayed = new Date(character.characterBase.dateLastPlayed).getTime();

                            if (!cacheTimestamp || cacheTimestamp >= lastPlayed) {
                                return;
                            }

                            delete cache.characters[characterId];
                            saveCache();
                        });

                        // index by characterId for ease of use
                        var characters = {};
                        angular.forEach(response.Response.data.characters, function(character) {
                            characters[character.characterBase.characterId] = character;
                        });

                        d.resolve({
                            characters: characters,
                            definitions: response.Response.definitions
                        });
                    });

                return d.promise;
            };

            this.getCharacterInventory = function(characterId) {
                var d = $q.defer();

                $http
                    .get('/api/characters/' + characterId + '/inventory?definitions=' + !this.hasCache(characterId))
                    .success(function(response) {
                        cacheDefinitions(characterId, response.Response.definitions);
                        d.resolve({buckets: response.Response.data.buckets, characterId: characterId});
                    });

                return d.promise;
            };

            this.getVault = function() {
                var d = $q.defer();

                $http
                    .get('/api/vault?definitions=' + !this.hasCache('vault'))
                    .success(function(response) {
                        cacheDefinitions('vault', response.Response.definitions);
                        d.resolve(response.Response.data.buckets);
                    });

                return d.promise;
            };

            /**
             * Aggregates inventory responses from the passed characters and assigns the characterId
             * to each item so it can be identified later.
             *
             * God method, baby.
             */
            this.getBucketAggregate = function(characterIds, vault) {
                var d = $q.defer();
                var promises = [];

                angular.forEach(characterIds, function(characterId) {
                    promises.push(this.getCharacterInventory(characterId));
                }.bind(this));

                if (typeof vault == "undefined" || vault == true) {
                    promises.push(this.getVault());
                }

                $q.all(promises).then(function(responses) {
                    angular.forEach(responses, function(response) {
                        if (response.characterId) {
                            onInventoryResponse(response.buckets, response.characterId);
                        } else {
                            onVaultResponse(response);
                        }
                    });
                    d.resolve(buckets);
                });

                return d.promise;
            };
        };
    });
