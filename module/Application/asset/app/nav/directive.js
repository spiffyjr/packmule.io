'use strict';

angular
    .module('app')
    .directive('navBar', function() {
        return {
            restrict: 'E',
            replace: true,
            scope: {},
            controller: function($rootScope, $scope, $http, $location, AuthService) {
                $scope.isCollapsed = true;
                $scope.hasIdentity = AuthService.hasIdentity;
                $scope.getIdentity = AuthService.getIdentity;

                $scope.go = function(path) {
                    $scope.isCollapsed = true;
                    $location.path(path);
                };

                $scope.onChangeCharacter = function(characterId) {
                    $scope.isCollapsed = true;
                    $rootScope.characterId = characterId;
                    $rootScope.$broadcast('characterChange', characterId);
                };

                var updateSession = function() {
                    AuthService.getSession();
                };

                $scope.$on('login.end', updateSession);
                $scope.$on('logout.end', updateSession);

                updateSession();
            },
            templateUrl: '/application/view/nav/directive.html'
        };
    });
