(function() {
    var app = angular.module('exampleApp', ['exampleAppDirective', 'exampleAppServices']);
    
    app.config(function (logServiceProvider) {
        logServiceProvider
                .debugEnabled(true)
                .messageCounterEnabled(true);
    });

    app.controller("defaultCtrl", function ($scope, logService) {
        $scope.data = {
            cities: ["London", "New York", "Paris"],
            totalClicks: 0
        };

        $scope.$watch('data.totalClicks', function (newVal) {
            logService.log("Total click count: " + newVal);
            });
    });
  
})();