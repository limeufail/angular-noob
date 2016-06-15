'use strict';
 
angular.module('News')

.factory("services", ['$http', function($http) {
  var serviceBase = 'php-services/'
  var obj = {};
  obj.getAllNews = function(){
        return $http.get(serviceBase + 'get_all_news');
  }
  
  obj.getNews = function(newsID){
        return $http.get(serviceBase + 'get_news?id=' + newsID);
  }

  obj.getCategory = function () {
        return $http.get(serviceBase + 'get_category');
  }

  obj.insertNews = function (newsData) {
    return $http.post(serviceBase + 'insert_news', newsData).then(function (results) {
        return results;
    });
  };

  obj.updateNews = function (id,news) {
    return $http.post(serviceBase + 'update_news', {id:id, news:news}).then(function (status) {
        return status.data;
    });
  };

  obj.removeNews = function (id) {
    return $http.delete(serviceBase + 'delete_news?id=' + id).then(function (status) {
     return status.data;
    });
  };

  return obj;

}]);