(function(){
    var app = angular.module('app', ['dynform', 'ShoppingService']);
    
    app.controller('AppCtrl', ['$scope', '$http', '$compile', 'ProductService', function ($scope, $http, $compile, ProductService) {
        ProductService.getForm()
            .success(function (data) {
                $scope.formTemplate = data.form.template;
                $scope.formData = data.form.data;
                
                $scope.processForm = function () {
                    ProductService.postForm($scope.formData, data.form.name)
                        .success(function(data){ 
                            console.log("Form has been posted successfully");
                        })
                        .error(function(data){
                            console.log("Error in form");
                            $scope.formTemplate = data.form.template;
                            $scope.formData = data.form.data;
                            
                            var element = angular.element(document.getElementById("my-form"));
                            element.html('<dynamic-form class="col-md-12" template="formTemplate" ng-model="formData" ng-submit="processForm()"></dynamic-form>');
                            $compile(element.contents())($scope);                            
                        });
                };
               
                var element = angular.element(document.getElementById("my-form"));
                element.html('<dynamic-form class="col-md-12" template="formTemplate" ng-model="formData" ng-submit="processForm()"></dynamic-form>');
                $compile(element.contents())($scope);
            });
    }]);
})();