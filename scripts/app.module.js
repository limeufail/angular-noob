'use strict';

// declare modules
angular.module('Home', []);
angular.module('Login', []);

angular.module('WebServicesApp', [
    'Authentication',
    'News',
    'Home',
    'Login',
    'ngRoute',
    'ngCookies'
])


