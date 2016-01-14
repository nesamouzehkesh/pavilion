(function() {
    var ShoppingService = angular.module('ShoppingService', []);
        
    ShoppingService.service('ProductService', ['$http', function ($http) {
        //simply returns the contacts list
        this.getForm = function () {
            return $http.get('../api/product/form/76');
        };
        
        this.postForm = function (formData, formName) {
            return $http({
                url: "../api/product/post",
                data: serializeData(formData, formName),
                method: 'POST',
                headers : {'Content-Type':'application/x-www-form-urlencoded; charset=UTF-8'}
            });
        };

        function serializeData(data, formName) { 
            // If this is not an object, defer to native stringification.
            if (!angular.isObject(data)) { 
                return((data == null) ? "" : data.toString()); 
            }

            var buffer = [];

            // Serialize each key in the object.
            for (var name in data) { 
                if (!data.hasOwnProperty(name)) { 
                    continue; 
                }

                var value = data[name];

                buffer.push(
                   formName + "[" + encodeURIComponent(name) + "]=" + encodeURIComponent((value == null) ? "" : value)
                ); 
            }

            // Serialize the buffer and clean it up for transportation.
            var source = buffer.join("&").replace(/%20/g, "+"); 
            return(source); 
        }                
    }]);
})();