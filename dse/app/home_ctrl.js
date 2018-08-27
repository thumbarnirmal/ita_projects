/**
 * Created by cfibqqz on 19/02/2016.
 */
angular.module('homeApp').controller('home_ctrl', function ($scope,Data,$location) {

    $scope.search_document=function(query){
        var dataObj = {'document_search_string':query};
        Data.post('searchDocument',dataObj).then(function (results) {
            if (results.code == 200) {
                $scope.search_result=results.response;
                console.log($scope.search_result);
            }else{
                console.log(results.message);
            }
        });
    }

});