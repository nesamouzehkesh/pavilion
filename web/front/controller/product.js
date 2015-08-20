(function() {
    var app = angular.module('product', []);
    
    app.directive('productImage', function() {
        return {
            restrict: 'E',
            templateUrl: '../directives/product-image.html'
        };
    });
    
})();