(function() {
    var ShoppingService = angular.module('ShoppingService', []);
        
    ShoppingService.service('ProductService', ['$http', function ($http) {
        //simply returns the contacts list
        this.getForm = function () {
            return $http.get('../api/product/form/1');
        };
    }]);
    
})();