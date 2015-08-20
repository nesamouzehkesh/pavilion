(function() {
    var calculatorService = angular.module('CalculatorService', []);
    
    calculatorService.service('MathService', function() {
        this.add = function(a, b) { return a + b };
        this.subtract = function(a, b) { return a - b };
        this.multiply = function(a, b) { return a * b };
        this.divide = function(a, b) { return a / b };
    });
    
    calculatorService.service('Calculator', function(MathService){
        this.square = function(a) { return MathService.multiply(a,a); };
        this.cube = function(a) { return MathService.multiply(a, MathService.multiply(a,a)); };
    });    
    
})();