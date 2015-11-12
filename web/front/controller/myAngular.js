(function() {
    var app = angular.module('myAngularApp', ['CalculatorService']);

    app.controller('CalculatorController', ['$scope', 'Calculator', function($scope, Calculator) {
        $scope.doSquare = function() {
            $scope.answer = Calculator.square($scope.number);
        }

        $scope.doCube = function() {
            $scope.answer = Calculator.cube($scope.number);
        }
    }]);    
    
    app.controller('LocaleController', function($scope, $locale) {
        $scope.locale = $locale.id;
    });
    
    app.controller('ItemsController', function($scope) {
        $scope.items = [
            {name: 'Item 1',
              items: [
               {name: 'Nested Item 1.1'},
               {name: 'Nested Item 1.2'}
              ]
            },
            {name: 'Item 2',
              items: [
               {name: 'Nested Item 2.1'},
               {name: 'Nested Item 2.2'}
              ]
            },
            {name: 'Item 3',
              items: [
               {name: 'Nested Item 3.1'},
               {name: 'Nested Item 3.2'},
               {name: 'Nested Item 3.3'}
              ]
            }
          ];
    });
    
    app.controller('EscapeController', function($scope, $element) {
        $scope.message = '';
        $element.bind('keyup', function (event) {
          if (event.keyCode === 27) { // esc key
            // Broken -- doesn't trigger UI update
            //$scope.message = '';
            $scope.$apply(function() {
                $scope.message = '';
            });
          }
        });
    });

    app.controller('SumController', function($scope) {
      $scope.values = [1, 2];
      $scope.newValue = 1;
      $scope.add = function() {
        $scope.values.push(parseInt($scope.newValue));
      };

      $scope.$watch('values', function () {
        $scope.sum = $scope.values.reduce(function(a, b) {
          return a + b;
        });
      }, true);
    });

    app.controller('NameController', function($scope) {
        $scope.name = "Name";
        $scope.parent = "Saman";
    });
    
    app.controller('ChildController', function($scope) {
        $scope.name = "Child";
    });

    app.controller('DeathrayMenuController', function($scope) {
        $scope.menuState = { show: true };
        
        $scope.toggleMenu = function() {
            $scope.menuState.show = !$scope.menuState.show;
        };
    });
    
    app.controller('AdditionController1', function($scope) {
        $scope.operand1 = 0;
        $scope.operand2 = 0;
        $scope.options = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        
        $scope.add = function() {
            return $scope.operand1 + $scope.operand2;
        };
    });
    
    app.controller('AdditionController2', function($scope) {
        $scope.operand1 = 0;
        $scope.operand2 = 0;
        $scope.options = [0, 1, 2, 3, 4, 5, 6, 7, 8, 9];
        
        $scope.add = function(operand1, operand2) {
            return operand1 + operand2;
        };
    });    
        
    app.controller('AdditionController3', function($scope, $rootScope) {
        $scope.operand1 = 0;
        $scope.operand2 = 0;
        $scope.operation = '+';
        $scope.operations = ['+', '-', '/', '*'];
        $scope.result = 0;
        
        updateRootScope = function ($rootScope) {
            console.log('yes watched on result');
            $rootScope.operand1 = $scope.operand1;
            $rootScope.operand2 = $scope.operand2;
            $rootScope.operation = $scope.operation;
            $rootScope.result = $scope.result;
        };
        // This part is not working
        $scope.$watch('result', updateRootScope);
        
        $scope.call = function() {
            var operand1 = parseInt($scope.operand1, 10);
            var operand2 = parseInt($scope.operand2, 10);
            var result = 0;
            switch ($scope.operation) {
                case '+': result = operand1 + operand2; break;
                case '-': result = operand1 - operand2; break;
                case '/': result = operand1 / operand2; break;
                case '*': result = operand1 * operand2; break;
                default: result = 0;
            }
            $scope.result = result;
        };
    });       
        
    app.controller('AdditionController4', function($scope, $rootScope) {
        $scope.operand1 = $rootScope.operand1;
        $scope.operand2 = $rootScope.operand2;
        $scope.operation = $rootScope.operation;
        $scope.result = $rootScope.result;
    });        
        
    
    app.controller('AuthController', function($scope) {
        $scope.authorized = true;
        
        $scope.toggle = function() {
            $scope.authorized = !$scope.authorized;
        };
    });

    app.controller('StartUpController', function($scope) {
        $scope.funding = { startingEstimate: 0 };
        
        computeNeeded = function() {
            $scope.funding.needed = $scope.funding.startingEstimate * 10;
        };
        
        $scope.reset = function() {
            $scope.funding.startingEstimate = 0;
        };
        
        $scope.$watch('funding.startingEstimate', computeNeeded);
        
        $scope.requestFunding = function() {
            window.alert("Sorry, please get more customers first.");
        };
    });
    
    app.controller('SomeTextController', function($scope) {
        $scope.someText = 'You have started your journey.';
    });
    
    app.controller('HelloController', function($scope) {
        $scope.greeting = { text: 'Hello' };
    });
    
    app.controller('HelloController2', function() {
        this.greeting = { text: 'Hello' };
    });
})();