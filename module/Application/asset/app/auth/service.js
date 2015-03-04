'use strict';

angular
    .module('app')
    .service('AuthService', function($rootScope, $q, $http) {
        var session = null;

        return new function() {
            this.hasIdentity = function() {
                return null !== session;
            };

            this.getIdentity = function() {
                return session;
            };

            this.login = function(data) {
                var deferred = $q.defer();

                $rootScope.$broadcast('login.start');

                $http
                    .post('/api/auth/login', data)
                    .success(function(result) {
                        $rootScope.$broadcast('logout.end', result);
                        deferred.resolve(result);
                    });

                return deferred.promise;
            };

            this.logout = function() {
                var deferred = $q.defer();

                $rootScope.$broadcast('logout.start');

                $http
                    .get('/api/auth/logout')
                    .success(function(result) {
                        session = null;
                        deferred.resolve(result);
                        $rootScope.$broadcast('logout.end', result);
                    });

                return deferred.promise;
            };

            this.getSession = function() {
                var deferred = $q.defer();

                if (this.hasIdentity()) {
                    deferred.resolve(session);
                } else {
                    $http
                        .get('/api/auth/session')
                        .success(function (data) {
                            session = Object.keys(data).length > 0 ? data : null;
                            $rootScope.$broadcast('session.update');
                            return deferred.resolve(session);
                        });
                }

                return deferred.promise;
            };
        };
    });
