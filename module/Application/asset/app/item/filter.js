'use strict';

angular
    .module('app')
    .filter('invalidBucket', function() {
        return function(buckets, definitions, removeSubclass) {
            var result = [];

            angular.forEach(buckets, function(bucket) {
                var definition = definitions.buckets[bucket.bucketHash];

                if (definition.category == 0 || definition.category == 2) {
                    return;
                }
                if (definition.bucketIdentifier == 'BUCKET_MISSION') {
                    return;
                }
                if (definition.bucketIdentifier == 'BUCKET_BOUNTIES') {
                    return;
                }
                if (definition.bucketIdentifier == 'BUCKET_BUILD' && removeSubclass) {
                    return;
                }
                result.push(bucket);
            });

            return result;
        };
    })
    .filter('bucketCategory', function() {
        return function(buckets, definitions, category) {
            var result = [];

            angular.forEach(buckets, function(bucket) {
                var definition = definitions.buckets[bucket.bucketHash];
                if (definition.category == category) {
                    result.push(bucket);
                }
            });

            return result;
        };
    })
    .filter('character', function() {
        return function(items, characterId) {
            var result = [];

            angular.forEach(items, function(item) {
                if (item.characterId == characterId) {
                    result.push(item);
                }
            });

            return result;
        }
    })
    .filter('notCharacter', function() {
        return function(items, characterId) {
            var result = [];

            angular.forEach(items, function(item) {
                if (item.characterId != characterId) {
                    result.push(item);
                }
            });

            return result;
        }
    })
    .filter('itemEquipped', function() {
        return function(items) {
            var result = [];

            angular.forEach(items, function(item) {
                if (item.isEquipped) {
                    result.push(item);
                }
            });

            return result;
        }
    })
    .filter('itemUnequipped', function() {
        return function(items) {
            var result = [];

            angular.forEach(items, function(item) {
                if (!item.isEquipped) {
                    result.push(item);
                }
            });

            return result;
        }
    })
    .filter('itemName', function() {
        return function(items, definitions, name) {
            var result = [];

            angular.forEach(items, function(item) {
                if (!name || definitions[item.itemHash].itemName.match(new RegExp(name, 'i'))) {
                    result.push(item);
                }
            });

            return result;
        };
    });
