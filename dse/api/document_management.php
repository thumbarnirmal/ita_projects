<?php

$app->post("/searchByAuthor", function ()  {

    $db = new DbHandler();

    if(!isset($_POST['document_detail'])){
        throw new Exception("Please enter valid document_detail as post data.");
    }

    $request = json_decode($_POST['document_detail']);

    if(verifyRequiredParams(array('document_author'), $request)){
        return ;
    }

    $document_author=$request->document_author;

    $IMG_BASE_URL=Utils::getImageBucketURL();
    $DOC_BASE_URL=Utils::getDocBucketURL();
    $sql_query = "select
                      document_id,
                      document_name,
                      CONCAT('$DOC_BASE_URL',document_url) AS document_url,
                      CONCAT('$IMG_BASE_URL',document_thumbnail_url) AS document_thumbnail_url,
                      document_author,
                      document_tag,
                      create_time,
                      update_time
                      FROM document_table
                      WHERE document_author LIKE '%".$document_author."%'";

    $r = $db->conn->query($sql_query) or die($this->mysqli->error.__LINE__);
    if ($r->num_rows > 0) {
        $result = array();
        while ($row = $r->fetch_assoc()) {
            $result[] = $row;
        }
        $response["code"] = 200;
        $response["message"] = "Document list successfully fetched.";
        $response["cause"] = "";
        $response["response"] = $result;
        echoResponse(200, $response);
        //echo $response;
    } else {
        $response["code"] = 200;
        $response["message"] = "Data Not Found.";
        $response["cause"] = "";
        $response["response"] = [];
        echoResponse(200, $response);
    }
});

$app->post("/searchDocument", function () use ($app) {

    $db = new DbHandler();

    /*if(!isset($_POST['document_detail'])){
        throw new Exception("Please enter valid document_detail as post data.");
    }*/

    $request = json_decode($app->request->getBody());
    //$request = $request->document_detail;

    /*if(verifyRequiredParams(array('document_search_string'), $request)){
        return ;
    }*/

    $document_search_string=$request->document_search_string;

    $IMG_BASE_URL=Utils::getImageBucketURL();
    $DOC_BASE_URL=Utils::getDocBucketURL();
    $sql_query = "select
                      document_id,
                      document_name,
                      CONCAT('$DOC_BASE_URL',document_url) AS document_url,
                      CONCAT('$IMG_BASE_URL',document_thumbnail_url) AS document_thumbnail_url,
                      document_author,
                      document_tag,
                      create_time,
                      update_time
                      FROM document_table
                      WHERE document_name LIKE '%".$document_search_string."%' OR document_tag LIKE '%".$document_search_string."%'";

    $r = $db->conn->query($sql_query) or die($this->mysqli->error.__LINE__);
    if ($r->num_rows > 0) {
        $result = array();
        while ($row = $r->fetch_assoc()) {
            $result[] = $row;
        }
        $response["code"] = 200;
        $response["message"] = "Document list successfully fetched.";
        $response["cause"] = "";
        $response["response"] = $result;
        echoResponse(200, $response);
        //echo $response;
    } else {
        $response["code"] = 200;
        $response["message"] = "Data Not Found.";
        $response["cause"] = "";
        $response["response"] = [];
        echoResponse(200, $response);
    }
});

$app->post("/deleteDocument", function () use ($app) {
    $db = new DbHandler();
    $response = array();

    try {
        $db->setAutoCommit(FALSE);
        /*if(!isset($_POST['document_detail'])){
            throw new Exception("Please enter valid document_detail as post data.");
        }*/
        /*if(!isset($_FILES['file'])){
            throw new Exception("Please upload valid document as post data.");
        }*/

        $request = json_decode($app->request->getBody());
        $document_id=$request->document_id;

        //$request = json_decode($_POST['document_id']);

        /*if(verifyRequiredParams(array('document_id'), $request)){
            return ;
        }*/

        //$document_name=$request->document_name;
        //$document_author=$request->document_author;
        //$document_tag=$request->document_tag;
        //$document_id=$request->document_id;
        //$document_tag_string="";

        $sql_query = "SELECT
                      document_url, document_thumbnail_url
                      FROM document_table
                      WHERE document_id = ".$document_id;

        $r = $db->conn->query($sql_query) or die($this->mysqli->error.__LINE__);
        //echo $r;
        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
                if($document_url = $row['document_url'])
                {
                    unlink("../documents/".$document_url);
                    echo $document_url;
                }

                if($document_thumbnail_url = $row['document_thumbnail_url'])
                {
                    unlink("../thumbnail_bucket/".$document_thumbnail_url);
                }
            }

            //echo "asdfasdfasdfasdfasdf";
            //$result = $r->fetch_assoc();
            //$result = fetchRowAsArray($r);
            //$result = $r->fetch_assoc();
            //echo $result;
            //$document_url = $result[0];
            //unlink($document_url);
            //$document_thumbnail_url = $result[1];
            //unlink($document_thumbnail_url);
            //echoResponse(200, $response);
            //echo $response;
        } else {
            $response["code"] = 200;
            $response["message"] = "Data Not Found.";
            $response["cause"] = "";
            $response["response"] = [];
            echoResponse(200, $response);
        }

        $sql_query_del_document = "DELETE FROM document_table WHERE document_id = ?";

        if (!($stmt = $db->conn->prepare($sql_query_del_document))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->bind_param("i",$document_id)) {
            throw new Exception("Binding parameters failed: (" . $stmt->errno . ")");
        }

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }

        $db->commit();
        $response["code"] = 200;
        $response["message"] = "Document deleted successfully.";
        $response["cause"] = "";
        $response["response"]['document_id'] = $document_id;
        echoResponse(200, $response);

    } catch (Exception $e) {
        $db->rollback();
        $response["code"] = 201;
        $response["cause"] = "error";
        $response["message"] = $e->getMessage();
        $response["response"] = json_decode("{}");//"Trace:" .$e->getTraceAsString();
        echoResponse(201, $response);
    }

    $db->setAutoCommit(TRUE);

});

$app->post("/updateDocument", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    //$document_tag=array();

    try {
        $db->setAutoCommit(FALSE);
        if(!isset($_POST['document_detail'])){
            throw new Exception("Please enter valid document_detail as post data.");
        }
        if(!isset($_FILES['file'])){
            throw new Exception("Please upload valid document as post data.");
        }

        $request = json_decode($_POST['document_detail']);

        if(verifyRequiredParams(array('document_name','document_author','document_id'), $request)){
            return ;
        }

        $document_name=$request->document_name;
        $document_author=$request->document_author;
        $document_tag=$request->document_tag;
        $document_id=$request->document_id;
        $document_tag_string="";

        for($i=0;$i<count($document_tag);$i++){
            if($i==0){
                $document_tag_string=$document_tag[$i];
            }else{
                $document_tag_string=$document_tag_string.",".$document_tag[$i];
            }
        }

        $sql_query_ins_document = "UPDATE document_table SET document_name = ?, document_author = ?, document_tag = ? WHERE document_id = ?";

        if (!($stmt = $db->conn->prepare($sql_query_ins_document))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->bind_param("ssss",$document_name,$document_author,$document_tag_string,$document_id)) {
            throw new Exception("Binding parameters failed: (" . $stmt->errno . ")");
        }

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }

        //$document_id=$stmt->insert_id;

        $sql_query = "SELECT
                      document_url, document_thumbnail_url
                      FROM document_table
                      WHERE document_id = ".$document_id;

        $r = $db->conn->query($sql_query) or die($this->mysqli->error.__LINE__);
        //echo $r;
        if ($r->num_rows > 0) {
            $result = array();
            while ($row = $r->fetch_assoc()) {
                $result[] = $row;
                if($document_url = $row['document_url'])
                {
                    unlink("../documents/".$document_url);
                    echo $document_url;
                }

                /*if($document_thumbnail_url = $row['document_thumbnail_url'])
                {
                    unlink("../thumbnail_bucket/".$document_thumbnail_url);
                }*/
            }

            //echo "asdfasdfasdfasdfasdf";
            //$result = $r->fetch_assoc();
            //$result = fetchRowAsArray($r);
            //$result = $r->fetch_assoc();
            //echo $result;
            //$document_url = $result[0];
            //unlink($document_url);
            //$document_thumbnail_url = $result[1];
            //unlink($document_thumbnail_url);
            //echoResponse(200, $response);
            //echo $response;
        } else {
            $response["code"] = 200;
            $response["message"] = "Data Not Found.";
            $response["cause"] = "";
            $response["response"] = [];
            echoResponse(200, $response);
        }

        $document_object=$_FILES['file'];
        $document_uid=$document_id . "document";
        $target_document_path="../documents/";
        $target_document_name=Utils::getNewFileName($document_object,$target_document_path,$document_uid);
        $document_status=Utils::uploadPDFFile($document_object,$target_document_path,$target_document_name);


        if(strcmp($document_status,"success")!=0){
            throw new Exception("System is unable to upload document.");
        }

        $sql_query_update_document = "UPDATE document_table SET document_url = ? WHERE document_id = ?";


        if (!($stmt = $db->conn->prepare($sql_query_update_document))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->bind_param("si",$target_document_name,$document_id)) {
            throw new Exception("Binding parameters failed: (" . $stmt->errno . ")");
        }


        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }

        $db->commit();
        $response["code"] = 200;
        $response["message"] = "Document updated successfully.";
        $response["cause"] = "";
        $response["response"]['document_id'] = $document_id;
        echoResponse(200, $response);

    } catch (Exception $e) {
        $db->rollback();
        $response["code"] = 201;
        $response["cause"] = "error";
        $response["message"] = $e->getMessage();
        $response["response"] = json_decode("{}");//"Trace:" .$e->getTraceAsString();
        echoResponse(201, $response);
    }

    $db->setAutoCommit(TRUE);
});

$app->post("/addDocument", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    //$document_tag=array();

    try {
        $db->setAutoCommit(FALSE);
        if(!isset($_POST['document_detail'])){
            throw new Exception("Please enter valid document_detail as post data.");
        }
        if(!isset($_FILES['file'])){
            throw new Exception("Please upload valid document as post data.");
        }

        $request = json_decode($_POST['document_detail']);

        if(verifyRequiredParams(array('document_name','document_author'), $request)){
            return ;
        }

        $document_name=$request->document_name;
        $document_author=$request->document_author;
        $document_tag=$request->document_tag;
        $document_tag_string="";

        for($i=0;$i<count($document_tag);$i++){
            if($i==0){
                $document_tag_string=$document_tag[$i];
            }else{
                $document_tag_string=$document_tag_string.",".$document_tag[$i];
            }
        }

        $sql_query_ins_document = "INSERT INTO document_table (document_name,document_author,document_tag) VALUES (?,?,?)";

        if (!($stmt = $db->conn->prepare($sql_query_ins_document))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->bind_param("sss",$document_name,$document_author,$document_tag_string)) {
            throw new Exception("Binding parameters failed: (" . $stmt->errno . ")");
        }

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }

        $document_id=$stmt->insert_id;

        $document_object=$_FILES['file'];
        $document_uid=$document_id . "document";
        $target_document_path="../documents/";
        $target_document_name=Utils::getNewFileName($document_object,$target_document_path,$document_uid);
        $document_status=Utils::uploadPDFFile($document_object,$target_document_path,$target_document_name);


        if(strcmp($document_status,"success")!=0){
            throw new Exception("System is unable to upload document.");
        }

        $sql_query_update_document = "UPDATE document_table SET document_url = ? WHERE document_id = ?";


        if (!($stmt = $db->conn->prepare($sql_query_update_document))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->bind_param("si",$target_document_name,$document_id)) {
            throw new Exception("Binding parameters failed: (" . $stmt->errno . ")");
        }


        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }

        $db->commit();
        $response["code"] = 200;
        $response["message"] = "Document added successfully.";
        $response["cause"] = "";
        $response["response"]['document_id'] = $document_id;
        echoResponse(200, $response);

    } catch (Exception $e) {
        $db->rollback();
        $response["code"] = 201;
        $response["cause"] = "error";
        $response["message"] = $e->getMessage();
        $response["response"] = json_decode("{}");//"Trace:" .$e->getTraceAsString();
        echoResponse(201, $response);
    }

    $db->setAutoCommit(TRUE);
});

$app->post("/addDocumentThumbnail", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    $document_tag=array();

    try {
        $db->setAutoCommit(FALSE);
        if(!isset($_POST['document_detail'])){
            throw new Exception("Please enter valid document_detail as post data.");
        }
        if(!isset($_FILES['file'])){
            throw new Exception("Please upload valid document_thumbnail as post data.");
        }
        $request = json_decode($_POST['document_detail']);

        if(verifyRequiredParams(array('document_id'), $request)){
            return ;
        }

        $document_id=$request->document_id;

        $document_thumbnail_object=$_FILES['file'];
        $document_thumbnail_uid=$document_id . "_document_thumbnail";
        $target_document_thumbnail_path="../thumbnail_bucket/";
        $target_document_thumbnail_name=Utils::getNewFileName($document_thumbnail_object,$target_document_thumbnail_path,$document_thumbnail_uid);
        $document_thumbnail_status=Utils::uploadImageFile($document_thumbnail_object,$target_document_thumbnail_path,$target_document_thumbnail_name);

        if(strcmp($document_thumbnail_status,"success")!=0){
            throw new Exception("System is unable to upload document thumbnail image.");
        }

        $sql_query_update_document_thumbnail_url = "UPDATE document_table SET document_thumbnail_url = ? WHERE document_id = ?";


        if (!($stmt = $db->conn->prepare($sql_query_update_document_thumbnail_url))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->bind_param("si",$target_document_thumbnail_name,$document_id)) {
            throw new Exception("Binding parameters failed: (" . $stmt->errno . ")");
        }


        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }

        $db->commit();
        $response["code"] = 200;
        $response["message"] = "Document added successfully.";
        $response["cause"] = "";
        $response["response"] = "";
        echoResponse(200, $response);

    } catch (Exception $e) {
        $db->rollback();
        $response["code"] = 201;
        $response["cause"] = "error";
        $response["message"] = $e->getMessage();
        $response["response"] = json_decode("{}");//"Trace:" .$e->getTraceAsString();
        echoResponse(201, $response);
    }
    $db->setAutoCommit(TRUE);
});

$app->post("/viewAllDocument", function ()  {

    $db = new DbHandler();
    $IMG_BASE_URL=Utils::getImageBucketURL();
    $DOC_BASE_URL=Utils::getDocBucketURL();
    $sql_query = "select
                      document_id,
                      document_name,
                      CONCAT('$DOC_BASE_URL',document_url) AS document_url,
                      CONCAT('$IMG_BASE_URL',document_thumbnail_url) AS document_thumbnail_url,
                      document_author,
                      document_tag,
                      create_time,
                      update_time
                      FROM document_table";

    $r = $db->conn->query($sql_query) or die($this->mysqli->error.__LINE__);
    if ($r->num_rows > 0) {
        $result = array();
        while ($row = $r->fetch_assoc()) {
            $result[] = $row;
        }
        $response["code"] = 200;
        $response["message"] = "Document list successfully fetched.";
        $response["cause"] = "";
        $response["response"] = $result;
        echoResponse(200, $response);
        //echo $response;
    } else {
        $response["code"] = 200;
        $response["message"] = "Data Not Found.";
        $response["cause"] = "";
        $response["response"] = [];
        echoResponse(200, $response);
    }
});

/* Write UPDATE Document API*/
/* Write DELETE Document API*/

/* Write SEARCH Document API*/

