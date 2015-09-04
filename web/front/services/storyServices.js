(function() {
    var StoryServices = angular.module('StoryServices', []);
    
    StoryServices.factory('ProductRepository', ['$http', function ($http) {
        return {
            getAllProducts: function() {
                return $http.get('../api/products');
            },
            deleteProduct: function(id) {
                return $http.delete('../api/product', {id: id});
            }
        };
    }]);

    StoryServices.factory('productFactory', function ($resource) {
        return $resource('../api/products/:id', {}, {
            show: { method: 'GET' },
            update: { method: 'PUT', params: {id: '@id'} },
            delete: { method: 'DELETE', params: {id: '@id'} }
        })
    });
})();