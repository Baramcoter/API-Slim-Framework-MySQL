<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

$configuration = [
    'settings' => [
        'displayErrorDetails' => true;
    ],
];

$config = new \Slim\Container($configuration);

//GET Method
$config->get('MyEndPoint/{parameter}', function(Request $request , Response $response){
    
    $parameter = $request->getAttribute('parameter');
    $sql = "SELECT column_name FROM table_name WHERE condition = '$parameter'";

    try{
        $db = new dbConn();
        $db = $db->connect();

        $statement = $db->query($sql);
        $data = $statement->fetchAll(PDO::FETCH_OBJ);

        $db = null;

        if(!isset($data)){
            return $response->withJson($data);
        }
    }
    catch(PDOException $ex){
        echo $ex->getMessage();
    }
});

//POST Method
$config->post('MyEndPoint', function(Request $request , Response $response){

    $parameter_A = $request->getParam('parameterA');
    $parameter_B = $request->getParam('parameterB');
    $parameter_C = $request->getParam('parameterC');
    $parameter_D = $request->getParam('parameterD');

    $sql = "INSERT INTO table_name(column_A, column_B, column_C, column_D)
            VALUES (:parameterA, :parameterB, :parameterC, :parameterD)";

    try{
        $db = new dbconn();
        $db = $db->connect();
        $statement = $db->prepare($sql);

        $statement->bindParam(':parameterA', $parameter_A);
        $statement->bindParam(':parameterB', $parameter_B);
        $statement->bindParam(':parameterC', $parameter_C);
        $statement->bindParam(':parameterD', $parameter_D);
        $statement->execute();

        $db = null;

        $messageOK = "Success";
        $messageNG = "Fail";

        if(isset($statement)){
            return $response->whitJson($messageOK);
        }
        else{
            return $response->withJson($messageNG);
        }
    }
    catch(PDOException $ex){
        return $response->withJson($ex->getMessage());
    }
});

//PUT Method
$config->put('MyEndPoint/{parameter}', function(Request $request , Response $response){

    $parameter_A = $request->getAttribute("parameter");
    $parameter_B = $request->getParam('parameterB');
    $parameter_C = $request->getParam('parameterC');

    $sql = "UPDATE table_name SET
            colomn_A = :parameterB,
            column_B = :parameterC
            WHERE condition = '$parameter_A'";

    try{
        $db = new dbconn();
        $db = $db->connect();
        $statement = $db->prepare($sql);

        $statement->bindParam(':parameterB', $parameter_B);
        $statement->bindParam(':parameterC', $parameter_C);
        $statement->execute();

        $db = null;

        $messageOK = "Success";
        $messageNG = "Fail";

        if(isset($statement)){
            return $response->whitJson($messageOK);
        }
        else{
            return $response->withJson($messageNG);
        }

    }
    catch(PDOException $ex){
        return $response->withJson($ex->getMessage());
    }
});

//DELETE Method
$config->delete('MyEndPoint/{parameter}', function(Request $request , Response $response){

    $parameter = $request->getAttribute('parameter');

    $sql = "DELETE FROM table_name
            WHERE condition = '$parameter'";
    
    try{
        $db = new dbconn();
        $db = $db->connect();

        $statement = $db->prepare($sql);
        $statement->execute();
        $db = null;

        $messageOK = "Success";
        $messageNG = "Fail";

        if(isset($statement)){
            return $response->whitJson($messageOK);
        }
        else{
            return $response->withJson($messageNG);
        }
    }
    catch(PDOException $ex){
        return $response->withJson($ex->getMessage());
    }
});