(function(){
    var app = angular.module('app', ['dynform', 'ShoppingService']);
    app.controller('AppCtrl', ['$scope', '$http', '$compile', 'ProductService', function ($scope, $http, $compile, ProductService) {

        ProductService.getForm()
            .success(function (data) {
                $scope.formTemplate = data.form.template;
                $scope.formData = data.form.data;
                $scope.processForm = function () {
                    console.log('yep ...');

                    $http({
                        url: "../api/product/post",
                        data: $scope.form,
                        method: 'POST',
                        headers : {'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'}

                    }).success(function(data){

                        console.log("OK", data);

                    }).error(function(err){"ERR", console.log(err)})                    
                    
                };
                
                var element = angular.element(document.getElementById("my-form"));
                element.html('<dynamic-form class="col-md-12" template="formTemplate" ng-model="formData" ng-submit="processForm()"></dynamic-form>');
                $compile(element.contents())($scope);
            });
    }]);
})();