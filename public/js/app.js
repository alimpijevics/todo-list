'use strict';

// Declare app level module which depends on filters, and services
angular.module('timeManagement', [
  'ngRoute',
  'restangular',
  'angular-flash.service',
  'angular-flash.flash-alert-directive',

  'timeManagement.services',
  'timeManagement.directives',
  'timeManagement.controllers'
]).
config(['$routeProvider', 'RestangularProvider', 'flashProvider', function($routeProvider, RestangularProvider, flashProvider) {
  // set base url for all api cals
  RestangularProvider.setBaseUrl('/api/v1');

  // added bootstrap class names
  flashProvider.errorClassnames.push('alert-danger');
  flashProvider.successClassnames.push('alert-success');

  // routes
  $routeProvider.when('/login', { templateUrl: 'templates/login.html', controller: 'LoginCtrl' });
  $routeProvider.when('/sign-up', { templateUrl: 'templates/sign-up.html', controller: 'SignupCtrl' });
  $routeProvider.when('/', {
    templateUrl: 'templates/home.html',
    controller: 'HomeCtrl',
    resolve: {
      times: [ 'TimesRepository', function(TimesRepository) {
        return TimesRepository.all();
      }]
    }
  });
  $routeProvider.otherwise({redirectTo: '/login'});
}])
.run(function($rootScope, $location, AuthService, Restangular) {
  // put api key (if exist) in http header for each api call
  Restangular.setDefaultHeaders({ 'X-ApiKey': AuthService.getApiKey() });

  // put current user (if exist) into root scope
  $rootScope.user = AuthService.getUser();

  // check can current user access url
  var routesThatRequireAuth = ['/'];
  $rootScope.$on('$routeChangeStart', function(event, next, current) {
    if (routesThatRequireAuth.indexOf($location.path()) !== -1 && !AuthService.isLoggedIn()) {
      $location.path('/login');
    } else if (routesThatRequireAuth.indexOf($location.path()) === -1 && AuthService.isLoggedIn()) {
      $location.path('/');
    }
  });
});


/* Services */

angular.module('timeManagement.services', [])
  .factory('AuthService', ['$http', '$window', '$rootScope', 'Restangular', function($http, $window, $rootScope, Restangular) {
    var saveApiKey = function(apiKey) {
      setToSessionStorage('apiKey', apiKey);
    };

    var rememberUser = function(user) {
      setToSessionStorage('user', angular.toJson(user));
      $rootScope.user = user;
      Restangular.setDefaultHeaders({ 'X-ApiKey': user.api_key });
    };

    var getFromSessionStorage = function(what) {
      return $window.sessionStorage[what];
    };
    var setToSessionStorage = function(key, val) {
      $window.sessionStorage[key] = val;
    };
    var deleteFromSessionStorage = function(key) {
      delete $window.sessionStorage[key];
    };

    return {
      authenticate: function(credentials) {
        return $http.post('/api/v1/authenticate', credentials).success(function(data) {
          saveApiKey(data.api_key);
          rememberUser(data);

        }).error(function(data) {
          deleteFromSessionStorage('apiKey');
        });
      },
      register: function(userData) {
        return $http.post('/api/v1/users', userData).success(function(data) {
          saveApiKey(data.api_key);
          rememberUser(data);
        });
      },
      isLoggedIn: function() {
        return getFromSessionStorage('apiKey');
      },
      logout: function() {
        Restangular.setDefaultHeaders({ 'X-ApiKey': null });
        $rootScope.user = null;
        deleteFromSessionStorage('apiKey');
        deleteFromSessionStorage('user');
      },
      getApiKey: function() {
        return getFromSessionStorage('apiKey');
      },
      getUser: function() {
        return angular.fromJson(getFromSessionStorage('user'));
      },
      updateUser: function(user) {
        setToSessionStorage('user', angular.toJson(user));
        $rootScope.user = user;
      }
    };
  }])
  .factory('TimesRepository', ['Restangular', function(Restangular) {
    return {
      all: function() {
        return Restangular.all('times').getList();
      },
      filter: function(query) {
        return Restangular.all('times').getList(query);
      }
    };
  }])
  .factory('UsersRepository', ['Restangular', function(Restangular) {
    return Restangular.service('users');
  }]);


/* Controllers */

angular.module('timeManagement.controllers', [])
  .controller('LoginCtrl', ['$scope', 'AuthService', '$location', function($scope, AuthService, $location) {
    $scope.credentials = {
      email: '',
      password: ''
    };

    $scope.login = function() {
      $scope.loginError = null;

      AuthService.authenticate($scope.credentials).success(function() {
        $location.path('/');
      }).error(function(data) {
        $scope.loginError = data.msg;
      });
    };
  }])
  .controller('SignupCtrl', ['$scope', 'AuthService', '$location', function($scope, AuthService, $location) {
    $scope.newUser = {
      full_name: '',
      email: '',
      password: '',
      confirm_password: '',
      preferred_working_hours: 8
    };

    $scope.register = function() {
      $scope.registerErrors = {};

      AuthService.register($scope.newUser).success(function() {
        $location.path('/');
      }).error(function(data) {
        $scope.registerErrors = data;
      });
    };

  }])
  .controller('HomeCtrl', ['$scope', 'AuthService', '$location', 'times', 'flash', 'TimesRepository', 'UsersRepository',
    function($scope, AuthService, $location, times, flash, TimesRepository, UsersRepository) {
      $scope.times = times;
      $scope.newTime = {
        worked_hours: '',
        date: '',
        notes: ''
      };
      
      $scope.pwh = AuthService.getUser().preferred_working_hours;

      $scope.logout = function() {
        AuthService.logout();
        $location.path('/login');
      };

      $scope.insertTime = function() {
        $scope.times.post($scope.newTime).then(function(time) {
          flash.success = 'New time record successful added.';
          $scope.timeErrors = null;
          $scope.times.push(time);

          $scope.newTime = {
            worked_hours: '',
            date: '',
            notes: ''
          };
        }, function(resp) {
          flash.error = 'Some error occured while saving new time record.';
          $scope.timeErrors = resp.data;
        });
      };

      var editInProgress = {};

      $scope.editTime = function(time) {
        editInProgress['time_' + time.id] = angular.copy(time);
        time.editing = true;
        time.date_formated = time.date.split('T')[0];
      };
      
      $scope.isInPreferredWorkingHours = function(time) {
        return parseFloat(time.worked_hours) >= parseFloat($scope.pwh);
      }

      $scope.cancelEditingTime = function(time) {
        var oldVersion = editInProgress['time_' + time.id];

        time.worked_hours = oldVersion.worked_hours;
        time.notes = oldVersion.notes;
        time.date = oldVersion.date;
        time.editing = false;
      };

      $scope.updateTime = function(time) {
        time.date = time.date_formated;

        time.put().then(function(updatedTime) {
          time.worked_hours = updatedTime.worked_hours;
          time.notes = updatedTime.notes;
          time.date = updatedTime.date;
          time.editing = false;
          time.errors = undefined;
        }, function(resp) {
          time.errors = resp.data;
        });
      };

      $scope.removeTime = function(time) {
        if (confirm('Are you sure that you want to delete this record?')) {
          time.remove().then(function() {
            _.remove($scope.times, { id: time.id });
          });
        }
      };

      $scope.filter = {
        from: '',
        to: '',
      };

      $scope.filterTimes = function() {
        TimesRepository.filter($scope.filter).then(function(filteredTimes) {
          $scope.times = filteredTimes;
        });
      };

      $scope.resetFilter = function() {
        $scope.filter = {
          worked_hours: '',
          date: '',
          notes: ''
        };
        TimesRepository.all().then(function(filteredTimes) {
          $scope.times = filteredTimes;
        });
      };
      
      $scope.updatePreferredWorkingHours = function() {
        $scope.pwhError = null;
        
        var olduser = AuthService.getUser();
        var user = UsersRepository.one(olduser.id);
        user.preferred_working_hours = $scope.pwh;
        user.put().then(function(user) {
          AuthService.updateUser(user);
        }, function(resp) {
          flash.error = 'Some error occured while preferred working hours.';
          $scope.pwhError = resp.data.preferred_working_hours;
        });
      }
  }]);


/* Directives */

angular.module('timeManagement.directives', [])
  .directive('ngEnter', function () {
    return function (scope, element, attrs) {
      element.bind("keydown keypress", function (event) {
        if(event.which === 13) {
          scope.$apply(function (){
            scope.$eval(attrs.ngEnter);
          });

          event.preventDefault();
        }
      });
    };
  });
