angular.module('WebServicesApp')

.config(['$routeProvider', function ($routeProvider) {

    $routeProvider
        .when('/login', {
            controller: 'LoginController',
            templateUrl: 'app/modules/login/views/login.html',
            hideMenus: true
        })
 
        .when('/', {
            title: 'Home',
            templateUrl: 'app/modules/home/views/home.html',
            controller: 'HomeController'
        })

        .when('/view/:newsID', {
            title: 'View News',
            templateUrl: 'app/modules/home/views/view.html',
            controller: 'ViewController'
        })

        .when('/edit/:newsID', {
            title: 'Edit News',
            templateUrl: 'app/modules/home/views/edit.html',
            controller: 'EditController',
            resolve: {
              news: function(services, $route){
                var newsID = $route.current.params.newsID;
                return services.getNews(newsID);
            }
        }
        })
 
        .otherwise({ redirectTo: '/login' });
}])
 
.run(['$rootScope', '$location', '$cookieStore', '$http',
    function ($rootScope, $location, $cookieStore, $http) {
        // keep user logged in after page refresh
        $rootScope.globals = $cookieStore.get('globals') || {};
        if ($rootScope.globals.currentUser) {
            $http.defaults.headers.common['Authorization'] = 'Basic ' + $rootScope.globals.currentUser.authdata; // jshint ignore:line
        }
 
        $rootScope.$on('$locationChangeStart', function (event, next, current) {
            // redirect to login page if not logged in
            if ($location.path() !== '/login' && !$rootScope.globals.currentUser) {
                $location.path('/login');
            }
        });
    }]);