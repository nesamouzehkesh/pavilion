(function(){
    var app = angular.module('app', ['dynform', 'ShoppingService']);


    app.controller('AppCtrl', ['$scope', '$http', 'ProductService', function ($scope, $http, ProductService) {
        $scope.stdFormData = {};
        $scope.stdFormTemplate = {};
        
        ProductService.getForm()
            .success(function (data) {
                //console.log(data.form.data);
                //$scope.stdFormData = data.form.data;
                //$scope.stdFormTemplate = data.form.template;
                $scope.stdFormTemplate = [
                    {
                        "type": "text",
                        "model": "name.first",
                        "label": "Last Name",
                    },
                    {
                        "type": "text",
                        "label": "Last Name",
                        "model": "name.last",
                    },
                    {
                        "type": "email",
                        "label": "Email Address",
                        "model": "email",
                    },
                    {
                        "type": "submit",
                        "label": "submit",
                        "model": "submit",
                    }
                ];

                $scope.stdFormData = {
                    "name": {
                        "first": "Saman",
                        "last": "Shafigh"
                        },
                    "email": "saman@gmail.com"
                };                 
            });
        
        /*
        $scope.stdFormTemplate = [
            {
                "type": "text",
                "model": "name.first",
                "label": "Last Name",
            },
            {
                "type": "text",
                "label": "Last Name",
                "model": "name.last",
            },
            {
                "type": "email",
                "label": "Email Address",
                "model": "email",
            },
            {
                "type": "submit",
                "label": "submit",
                "model": "submit",
            }
        ];

        $scope.stdFormData = {
            "name": {
                "first": "Saman",
                "last": "Shafigh"
                },
            "email": "saman@gmail.com"
        };   
        */
    }]);    
})();