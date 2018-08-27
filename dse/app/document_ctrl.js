/**
 * Created by cfibqqz on 06/04/2016.
 */
angular.module('homeApp').controller('document_master_ctrl', function ($scope,Data,$location,$window) {
    console.log("welcome admin in document_master_ctrl");

    $scope.add_new_document=function(){
        $location.path("/add_document");
    }
//////////////////////////////////////////////////////////////////
//    $scope.update_document=function(){
//        $location.path("/update_document");
//    }
//////////////////////////////////////////////////////////////////
    $scope.delete_document=function(id){
        var dataobj = {'document_id':id};
        //console.log("JSON : "+dataobj.search.search_query);
        Data.post('deleteDocument',dataobj).then(function (results) {
            if (results.code == 200) {
                console.log(results.response);
            }else{
                console.log(results.response);
            }
        });
        $location.path("/document_master");
        location.reload();
    }
    $scope.update_document=function($document_id){
        $location.path("/update_document/"+$document_id);
    };
    Data.post('viewAllDocument').then(function (results) {
            if (results.code == 200) {
                $scope.document_master=results.response;
                console.log($scope.document_master);
            }else{
                console.log(results.message);
            }
    });
    $scope.download= function(url){
        $window.location.href=url;
    }
});

angular.module('homeApp').controller('add_document_ctrl', function ($scope,$location, Upload) {
    console.log("welcome admin in add_document_ctrl");
    $scope.go_back= function () {
        $location.path("/document_master");
    }
    
    $scope.tags=[
        {
            "tag_name":"Information Security"
        },
        {
            "tag_name":"Computer Networks"
        },
        {
            "tag_name":"ITA"
        },
        {
            "tag_name":"Software Tools"
        },
        {
            "tag_name":"DBMS"
        }
    ]

    $scope.document_master={
        'document_cover_img':'',
        'document':''
    }


    $scope.$watch('document_master.document', function () {
        $scope.document_temp_name= $scope.document_master.document.name;
    });

    $scope.uploadFile= function (files) {
        $scope.document_master.document_cover_img=files;
    }

    $scope.save_details= function (document_master) {
        console.log(document_master);

        var document_detail={
            "document_name":document_master.document_name,
            "document_author":document_master.document_author,
            "document_tag":document_master.document_tag
        }
        Upload.upload({
            url: './api/addDocument',
            fields: {
                'document_detail': JSON.stringify(document_detail)
            },
            file: document_master.document
        }).success(function (results, status, headers, config) {
            if (results.code == 200) {
                var document_id=results.response.document_id;

                var document_detail={
                    "document_id":document_id
                }
                Upload.upload({
                    url: './api/addDocumentThumbnail',
                    fields: {
                        'document_detail': JSON.stringify(document_detail)
                    },
                    file: document_master.document_cover_img[0]
                }).success(function (results, status, headers, config) {
                    if (results.code == 200) {
                        console.log(results.message);
                        $scope.error_message=results.message;
                        $location.path("/document_master");
                    } else {
                        console.log(results.message);
                        $scope.error_message=results.message;
                    }
                });
            } else {
                console.log(results.message);
                $scope.error_message=results.message;
            }
        });
    }

    $scope.update_details= function (document_master) {
        console.log(document_master);

        var document_detail={
            "document_id":document_master.document_id,
            "document_name":document_master.document_name,
            "document_author":document_master.document_author,
            "document_tag":document_master.document_tag
        }
        Upload.upload({
            url: './api/updateDocument',
            fields: {
                'document_detail': JSON.stringify(document_detail)
            },
            file: document_master.document
        }).success(function (results, status, headers, config) {
            if (results.code == 200) {
                var document_id=results.response.document_id;
                console.log("asdfasdfasdfasdf");

                var document_detail={
                    "document_id":document_id
                }
                Upload.upload({
                    url: './api/addDocumentThumbnail',
                    fields: {
                        'document_detail': JSON.stringify(document_detail)
                    },
                    file: document_master.document_cover_img[0]
                }).success(function (results, status, headers, config) {
                    if (results.code == 200) {
                        console.log(results.message);
                        console.log("qwerqwerqwerqwer");
                        $scope.error_message=results.message;
                        $location.path("/document_master");
                    } else {
                        console.log(results.message);
                        $scope.error_message=results.message;
                    }
                });
            } else {
                console.log(results.message);
                $scope.error_message=results.message;
            }
        });
    }

});

angular.module('homeApp').controller('update_document_ctrl/:document_id', function ($scope ,$location, Upload, $route, $routeParams) {
    console.log("welcome admin in update_document_ctrl");
    $scope.go_back= function () {
        $location.path("/document_master");
    };
//    console.log($routeParams.document_id);
    var $document_id=$routeParams.document_id;
//  console.log($route.current.params.document_id);

//  $scope.master = Sdata;
//  $scope.showAlert = function(data){
//      console.log(data.target.id);
//  }
//console.log($scope.master.document_author);
    $scope.tags=[
        {
            "tag_name":"Information Security"
        },
        {
            "tag_name":"Computer Networks"
        },
        {
            "tag_name":"ITA"
        },
        {
            "tag_name":"Software Tools"
        },
        {
            "tag_name":"DBMS"
        }
    ];

    $scope.document_master={
        'document_cover_img':'',
        'document':''
    };

    $scope.$watch('document_master.document', function () {
        $scope.document_temp_name= $scope.document_master.document.name;
    });

    $scope.uploadFile= function (files) {
        $scope.document_master.document_cover_img=files;
    };

    $scope.save_details= function (document_master) {
//        console.log(document_master);

        var document_detail={
            "document_name":document_master.document_name,
            "document_author":document_master.document_author,
            "document_tag":document_master.document_tag,
            "document_id":$document_id
        };
//        console.log(document_detail);
        Upload.upload({
            url: './api/updateDocument',
            fields: {
                'document_detail': JSON.stringify(document_detail)
            },
            file: document_master.document
        }).success(function (results, status, headers, config) {
            if (results.code == 200) {
                var document_id=results.response.document_id;

                var document_detail={
                    "document_id":document_id
                };
                Upload.upload({
                    url: './api/addDocumentThumbnail',
                    fields: {
                        'document_detail': JSON.stringify(document_detail)
                    },
                    file: document_master.document_cover_img[0]
                }).success(function (results, status, headers, config) {
                    if (results.code == 200) {
                        console.log(results.message);
                        $scope.error_message=results.message;
                        $location.path("/document_master");
                    } else {
                        console.log(results.message);
                        $scope.error_message=results.message;
                        $location.path("/document_master");
                    }
                });
            } else {
                console.log(results.message);
                $scope.error_message=results.message;
                $location.path("/document_master");
            }
        });
    }
});