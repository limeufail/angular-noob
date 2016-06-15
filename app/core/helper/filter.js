angular.module('WebServicesApp')
.filter('wordLimit', ['$filter', function($filter) {
   return function(input, limit) {
      if (! input) return;
      if (input.length <= limit) {
          return input;
      }
      return input.split(/\s+/).slice(0,limit).join(" ");
   };
}])