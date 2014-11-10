angular.module('app', ['ngRoute'])
.config( ['$routeProvider', '$locationProvider', 
function($routeProvider, $locationProvider) {
  $routeProvider
    .when('/', {
      templateUrl: 'index/problems.html',
      controller: 'ProblemsCtrl',
      controllerAs: 'problems'
    })
    .when('/problem/', {
      templateUrl: 'index/problems.html',
      controller: 'ProblemsCtrl',
      controllerAs: 'problems'
    })
   .when('/problem/:id', {
      templateUrl: 'index/problem.html',
      controller: 'ProblemCtrl',
      controllerAs: 'problem'
    })
   .when('/problem/:id/podium', {
      templateUrl: 'index/podium.html',
      controller: 'PodiumCtrl',
      controllerAs: 'podium'
    })
    .when('/problem/:id/submission', {
      templateUrl: 'index/submission.html',
      controller: 'SubmissionCtrl',
      controllerAs: 'submission'
    })

  $locationProvider.html5Mode(true);
}])

.controller('MainCtrl', ['$route', '$routeParams', '$location', 
 function($route, $routeParams, $location) {
    this.$route = $route;
    this.$location = $location;
    this.$routeParams = $routeParams;
}])
.factory('ApiRequest', ['$http','$q',function($http, $q){
  var get = function(url) {
      var ret = $q.defer();
      $http.get('app.php/' + url).
      success(function(data, status, headers, config) {
        ret.resolve(data);
      }).
      error(function(data, status, headers, config) {
        alert('Some error, try later!' + data);
        ret.reject(status);
      });
      return ret.promise;
  };

  return {get: get};
}])


.controller('ProblemsCtrl', ['ApiRequest','$scope','$routeParams', function(apiRequest, $scope, $routeParams) {
  apiRequest.get('problem').then(function(data){
    $scope.data = data;
  });
}])

.controller('ProblemCtrl', ['ApiRequest','$scope','$routeParams', function(apiRequest, $scope, $p) {
  apiRequest.get('problem/'+ $p.id).then(
    function(data){
    $scope.data = data;
  });
}])

.controller('PodiumCtrl', ['ApiRequest','$scope','$routeParams', function(apiRequest, $scope, $routeParams) {
  apiRequest.get('podium/').then(function(data){
    $scope.data = data;
  });
}])

.controller('SubmissionCtrl', ['ApiRequest','$scope','$routeParams', function(apiRequest, $scope, $routeParams) {

}])
