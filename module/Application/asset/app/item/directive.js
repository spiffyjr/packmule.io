'use strict';

angular
    .module('app')
    .directive('itemIcon', function() {
        return {
            restrict: 'E',
            replace: true,
            scope: {
                item: '=',
                itemDefinitions: '=',
                characterDefinitions: '=',
                activeCharacter: '=',
                characters: '=',
                definitions: '=',
                transferToVault: '='
            },
            controller: function($rootScope, $scope, $q, $http, $timeout, growl, ngDialog) {
                var broadcastUpdate = function(characters, vault) {
                    ngDialog.close();
                    growl.info('Transfer complete');
                    $rootScope.$broadcast('update.end', {characters: characters, vault: vault});
                };

                var doVault = function(item, definition, characterId, transferToVault) {
                    var d = $q.defer();

                    if (item.isEquipped) {
                        growl.error('Cannot move equipped items');
                        d.reject('Cannot move equipped items');
                        return;
                    }

                    var data = {
                        characterId: characterId,
                        itemId: item.itemInstanceId,
                        itemReferenceHash: item.itemHash,
                        membershipType: 2,
                        stackSize: item.stackSize,
                        transferToVault: transferToVault
                    };

                    $http
                        .post('/api/items/transfer', data)
                        .success(function(response) {
                            d.resolve(response);
                        })
                        .error(function(response) {
                            d.reject(response);
                        });

                    return d.promise;
                };

                $scope.itemCharacter = $scope.characters[$scope.item.characterId];
                $scope.itemDefinition = $scope.itemDefinitions.items[$scope.item.itemHash];
                $scope.icon = 'https://www.bungie.net/' + $scope.itemDefinition.icon;

                $scope.onTap = function() {
                    if ($scope.item.isEquipped) {
                        return;
                    }

                    ngDialog.open({
                        template: '/application/view/item/icon-popup.html',
                        className: 'item-popup',
                        scope: $scope,
                        showClose: false
                    });
                };

                $scope.onMove = function(item, definition, characterId) {
                    // item is on character, single move to vault, ez-pz
                    if (item.characterId == characterId) {
                        doVault(item, definition, characterId, true)
                            .then(function() {
                                broadcastUpdate([characterId], true);
                            });
                        return;
                    }

                    // item is in vault, single move to character, ez-pz
                    if (!item.characterId) {
                        doVault(item, definition, characterId, false)
                            .then(function() {
                                broadcastUpdate([characterId], true);
                            });
                        return;
                    }

                    // move from one character to another going through vault first with one second delay
                    // still ez-pz, brah
                    doVault(item, definition, item.characterId, true)
                        .then(function() {
                            var d = $q.defer();

                            $timeout(function() {
                                doVault(item, definition, characterId, false)
                                    .then(function(response) {
                                        d.resolve(response);
                                    });
                            }, 1000);

                            return d.promise;
                        })
                        .then(function() {
                            broadcastUpdate([item.characterId, characterId], true);
                        });
                };

                $scope.onEquip = function(item, definition, characterId) {
                    if (item.characterId != characterId) {
                        growl.error('How did you manage this voodoo?');
                        return;
                    }

                    if (item.isEquipped) {
                        growl.error('Item is already equipped');
                        return;
                    }

                    if (item.cannotEquipReason == 1) {
                        growl.error('Cannot equip: unknown reason');
                        return;
                    }

                    if (item.cannotEquipReason == 2) {
                        growl.error('You already have an exotic equipped');
                        return;
                    }

                    var data = {
                        characterId: characterId,
                        itemId: item.itemInstanceId,
                        membershipType: 2
                    };

                    $http
                        .post('/api/items/equip', data)
                        .success(function() {
                            broadcastUpdate([characterId], false);
                        });
                };
            },
            templateUrl: '/application/view/item/icon-directive.html'
        };
    });
