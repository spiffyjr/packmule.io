'use strict';

angular.extendDeep = function extendDeep(dst) {
    angular.forEach(arguments, function(obj) {
        if (obj !== dst) {
            angular.forEach(obj, function(value, key) {
                if (dst[key] && dst[key].constructor && dst[key].constructor === Object) {
                    extendDeep(dst[key], value);
                } else {
                    dst[key] = value;
                }
            });
        }
    });
    return dst;
};

angular
    .module('app', [
        'ngAnimate',
        'ngDialog',
        'ngRoute',
        'angular-loading-bar',
        'hmTouchEvents',
        'angular-growl',
        'ui.bootstrap'
    ])
    .config(function($locationProvider, $routeProvider) {
        $routeProvider
            .when('/', {
                templateUrl: '/application/view/index/index.html',
                controller: 'IndexCtrl'
            })
            .when('/login', {
                templateUrl: '/application/view/auth/login.html',
                controller: 'LoginCtrl'
            })
            .when('/logout', {
                templateUrl: '/application/view/index/index.html',
                controller: 'LogoutCtrl'
            })
            .otherwise({redirectTo: '/'});
    })
    .config(['growlProvider', function(growlProvider) {
        growlProvider.globalDisableCountDown(true);
        growlProvider.globalDisableCloseButton(true);
        growlProvider.globalDisableIcons(true);
        growlProvider.globalTimeToLive(3500);
        growlProvider.globalPosition('bottom-center');
    }]);
