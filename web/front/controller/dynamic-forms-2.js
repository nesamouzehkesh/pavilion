    (function(){
        var app = angular.module('app', ['dynform']);

        app.controller('AppCtrl', function ($http, $compile, $scope) {
            $http.get('../api/product/form/1').
                success(function(data) {
                    $scope.stdFormData = data.form.data;
                    $scope.stdFormTemplate = data.form.template;

                    var element = angular.element(document.getElementById("my-form"));
                    element.html('<dynamic-form class="col-md-12" template="stdFormTemplate" ng-model="stdFormData"></dynamic-form>');
                    $compile(element.contents())($scope);                
                });   
        });
    })();