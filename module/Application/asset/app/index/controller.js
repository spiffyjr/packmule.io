'use strict';

angular
    .module('app')
    .controller('IndexCtrl', function($http, $scope, ApiService, AuthService) {
        var updateBuckets = function(ids, vault) {
            ApiService
                .getBucketAggregate(ids, vault)
                .then(function(buckets) {
                    $scope.buckets = buckets;
                    $scope.definitions = ApiService.getDefinitions();
                });
        };

        var updateCharacters = function() {
            if (!AuthService.hasIdentity()) {
                return;
            }

            ApiService
                .getCharacters()
                .then(function(response) {
                    var ids = [];
                    angular.forEach(response.characters, function(character) {
                        ids.push(character.characterBase.characterId);
                    });

                    $scope.characterDefinitions = response.definitions;
                    $scope.characters = response.characters;
                    $scope.activeCharacter = $scope.characters[Object.keys($scope.characters)[0]];

                    updateBuckets(ids, true);
                });
        };

        $scope.activeCharacter = null;

        $scope.bucketOrder = function(bucket) {
            return $scope.definitions.buckets[bucket.bucketHash].bucketOrder;
        };

        $scope.getCharacterLabel = function(character) {
            var parts = [
                character.characterLevel,
                $scope.characterDefinitions.genders[character.characterBase.genderHash].genderName,
                $scope.characterDefinitions.races[character.characterBase.raceHash].raceName,
                $scope.characterDefinitions.classes[character.characterBase.classHash].className
            ];

            return parts.join(' ');
        };

        $scope.$on('session.update', function() {
            updateCharacters();
        });

        $scope.$on('update.end', function(event, params) {
            updateBuckets(params.characters, params.vault);
        });

        updateCharacters();
    });
