(function(){
    var app = angular.module('user', []);
    
    app.controller('HelloController', function($http, $scope){
        $http.get('../api/products/1').
            success(function(data) {
                $scope.user = data.user;
        });
    });
    
    app.controller('ListController', function($http, $scope){
        $http.get('../api/products').
            success(function(data) {
                $scope.products = data.products;
        });
    });
    
})();