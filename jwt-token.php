<?php
header("Access-Control-Allow-Origin: * ");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
header("Access-Control-Allow-Credentials: true");

use \Firebase\JWT\JWT;
use \Psr\Http\Message\ServerRequestInterface as Request;
use \Psr\Http\Message\ResponseInterface as Response;


$app = new \Slim\App;

//Create Authorize
$app->post('/api/test/auth', function(Request $request , Response $response){

    $identified = $request->getParam('id');
    $password = $request->getParam('pw');

    $sql = "INSERT INTO token(id, pw) VALUES(:id, :pw)"

    try{
        $db = new dbconn();
        $db = $db->connect();
        $statement = $db->prepare($sql);

        $password_hash = password_hash($password, PASSWORD_BCRYPT);

        $statement->bindParam(':id', $identified);
        $statement->bindParam(':pw', $password);
        $statement->execute();

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

//Issuing Token
$app->post('/api/test/token', function(Request $request , Response $response){

    $identified = $request->getParam('id');
    $password = $request->getParam('pw');

    $sql = "SELECT id, pw FROM token WHERE id = '$identified'";
});