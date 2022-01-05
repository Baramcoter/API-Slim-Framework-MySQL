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

    $data = json_decode(file_get_contents("php://input"));

    try{
        $db = new dbconn();
        $db = $db->connect();

        $statement = $db->query($sql);
        $num = $statement->rowCount();

        if($num > 0){
            $row = $statement->fetch();

            $contrast_identified = $row['id'];
            $contrast_password = $row['pw'];

            $db = null;

            if(password_verify($password, $contrast_password)){
                $secret_key = "Your secret key"; //any string
                $issuer_claim = "Your server name"; // this can be the servername
                $audience_claim = "THE_AUDIENCE";
                $issuedat_claim = time();
                $notbefore_claim = $issuedat_claim + 0; //<-Seconds, Waiting time for token acceptance after token creation
                $expire_claim = $issuedat_claim + 3600; //<-Seconds, Validify of the generated token

                $token = array(
                    "iss" => $issuer_claim,
                    "aud" => $audience_claim,
                    "iat" => $issuedat_claim,
                    "nbf" => $notbefore_claim,
                    "exp" => $expire_claim,
                    "data" => array(
                        "id" => $contrast_identified;
                    ));

                    http_response_code(200);

                    $jwt = JWT::encode($token, $secret_key);

                    echo json_encode(
                        array(
                            "message" => "Succcess",
                            "jwt" => $jwt,
                            "id" => $contrast_identified,
                            "expireAt" => $expire_claim),
                            JSON_PRETTY_PRINT);
            }
            else{
                echo json_encode(
                    array(
                        "message" => "failed",
                        "password" => $password), 
                        JSON_PRETTY_PRINT);
            }
        }
    }
    catch(PDOException $ex){
        return $ex->getMessage();
    }
});