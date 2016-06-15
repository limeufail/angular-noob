'use strict';
 
angular.module('Home')
 
.controller('HomeController',
    ['$scope',
    function ($scope) {
      
    }])

.controller('ListController',
    ['$scope','services',
    function ($scope, services) {
      services.getAllNews().then( function(data) {
      	$scope.list = data.data
      })
    }])

.controller('ViewController', function ($scope, $routeParams, services) {
    var newsID = ($routeParams.newsID) ? parseInt($routeParams.newsID) : 0;
    services.getNews(newsID).then(function(data){ 
        $scope.newslist_id = newsID;
        $scope.newslist = data.data;
    }); 
})

.controller('EditController', function ($scope, $rootScope, $location, $routeParams, services, news) {
    var newsID = ($routeParams.newsID) ? parseInt($routeParams.newsID) : 0;
    //$rootScope.title = (newsID > 0) ? 'Edit News' : 'Add News';
    $scope.buttonText = (newsID > 0) ? 'Update' : 'Add New';
      var original = news.data;
      original._id = newsID;
      $scope.news = angular.copy(original);
      $scope.news._id = newsID;
      services.getCategory(newsID).then(function(data){ 
        $scope.news_category = data.data;
      });
      $scope.isClean = function() {
        return angular.equals(original, $scope.news);
      }

      $scope.deleteNews = function(news) {
        $location.path('/');
        if(confirm("Proceed deleting news with id: "+$scope.news._id)==true)
        services.removeNews($scope.news._id);
      };

      $scope.saveNews = function(news) {
        $location.path('/');
        if (newsID <= 0) {
            services.insertNews(news);
        }
        else {
            services.updateNews(newsID, news);
        }
    };
})

