angular.module('app')

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


.controller('loginCtrl', ['ApiRequest','$location','$routeParams', function(apiRequest, $location, $p) {
  apiRequest.get('login').then(function(data){
    $location.path('/problems');
  });
}])

.controller('ProblemsCtrl', ['ApiRequest','$scope','$routeParams', function(apiRequest, $scope, $p) {
  apiRequest.get('problem').then(function(data){
    $scope.data = data;
  });
}])

.controller('ProblemEditCtrl', ['ApiRequest','$scope','$routeParams', function(apiRequest, $scope, $p) {
  apiRequest.get('problem/' + $p.id).then(function(data){
    $scope.data = data;
  });
}])

.controller('ProblemCreateCtrl', ['ApiRequest','$scope','$routeParams', function(apiRequest, $scope, $p) {
  
}])
