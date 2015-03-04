'use strict';

angular
    .module('app')
    .controller('LoginCtrl', function($scope, $location, AuthService) {
        $scope.onSubmit = function(data) {
            AuthService.login(data).then(function() {
                $location.path('/');
            });
        };
    })
    .controller('LogoutCtrl', function($rootScope, $location, AuthService, ApiService) {
        ApiService.clearCache();
        AuthService.logout().then(function() {
            $location.path('/');
        });
    });
