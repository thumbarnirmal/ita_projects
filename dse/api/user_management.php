<?php

$app->post("/addUser", function () use ($app) {
    $db = new DbHandler();
    $response = array();
    $request = json_decode($app->request->getBody());
    var_dump($request);

    $creation_date=Utils::getCurrentDate();
    $updated_date = $creation_date;
    session_start();
    try {
        $db->setAutoCommit(FALSE);
        $sql = "INSERT INTO restapi (user_name,user_email) VALUES ('$request->user_name','$request->user_email')";

        if (!($stmt = $db->conn->prepare($sql))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }
        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }
        else
        {
            $response["status"] = "success";
            $response["message"] = "User added successfully.";
            $response["cause"] = "";
            $response["response"]="";
            $db->commit();
        }
        echoResponse(200, $response);
    } catch (Exception $error) {
        $db->rollback();
        $response["status"] = "error";
        $response["message"] = "Server Not able to add user";
        $response["cause"] = "Exception:" . $error->getMessage();
        $response["response"] = "Trace:" . $error->getTraceAsString();
        echoResponse(201, $response);
    }
});

$app->post("/viewAllUsers", function ()  {

    $db = new DbHandler();
    $sql_query = "select * FROM restapi";
    $r = $db->conn->query($sql_query) or die($this->mysqli->error.__LINE__);
    if ($r->num_rows > 0) {
        $result = array();
        while ($row = $r->fetch_assoc()) {
            $result[] = $row;
        }
        $response["status"] = "success";
        $response["message"] = "User list successfully fetched.";
        $response["cause"] = "";
        $response["response"] = $result;
        echoResponse(200, $response);
    } else {
        $response["status"] = "success";
        $response["message"] = "Data Not Found.";
        $response["cause"] = "";
        $response["response"] = [];
        echoResponse(200, $response);
    }
});

$app->post("/findUserByTag", function() use ($app)  {

    $db = new DbHandler();
    $response = array();
    $request = json_decode($app->request->getBody());

    verifyRequiredParams(array('search_query'), $request);

    $search_query=$request->search_query;

    $newParameter='%'.$search_query.'%';
    $column_name="user_name";
    $table_prefix="";
    try {

        $sql_query_search_user_name = "SELECT  * FROM restapi WHERE $column_name LIKE ?";

        //echo "Query:".$sql_query_search_user_name;

        if (!($stmt = $db->conn->prepare($sql_query_search_user_name))) {
            throw new Exception("Prepare failed: (" . $db->conn->errno . ") ");
        }

        if (!$stmt->bind_param("s",$newParameter)) {
            throw new Exception("Binding parameters failed: (" . $stmt->errno . ")");
        }

        if (!$stmt->execute()) {
            throw new Exception("Execute failed: (" . $stmt->errno . ") ");
        }


        $row = Utils::fetchRowAsArray($stmt);
        if ($row == null) {
            $row = array();
        }

        $response["code"] = 200;
        $response["message"] = "User list successfully fetched.";
        $response["cause"] = "";
        $response["response"]["user_list"] = $row;
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

