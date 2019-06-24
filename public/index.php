<?php

use Psr\Http\Message\ServerRequestInterface as Request;
use Psr\Http\Message\ResponseInterface as Response;

require '../vendor/autoload.php';
require '../includes/DbOperations.php';

$app = new \Slim\App([
    'settings'=>[
        'displayErrorDetails'=>true 
    ]
]);

//my api call 
//end point createuser
//parameter email, passowrd
//post method for inserting into database

$app->post('/createuser',function(Request $request, Response $response){

    if(!haveEmptyParam(array('email', 'password'),$request,$response)){   

       $request_data = $request->getParsedBody(); 
       $email = $request_data['email'];
       $password  =$request_data['password'];

       $db = new DbOperations;

       $hash_passowrd =  password_hash($password,PASSWORD_DEFAULT);
       $result = $db->createUser($email,$hash_passowrd);
       
       if($result == USER_CREATED){

        $message = array();
        $message['error']= false;
        $message['message']= 'User created successfully';

        $response->write(json_encode($message));

         return $response
         ->withHeader('Content-type','application/json')
         ->withStatus(201);

       }else if( $result == USER_FAILURE){
           
        $message['error']= true;
        $message['message']= 'Error';

        $response->write(json_encode($message));

         return $response
         ->withHeader('Content-type','application/json')
         ->withStatus(422);

       }else if($result ==  USER_EXIST){

        $message['error']= true;
        $message['message']= 'Error';

        $response->write(json_encode($message));

         return $response
         ->withHeader('Content-type','application/json')
         ->withStatus(422);
       }

       
       $message['error']= true;
       $message['message']= 'Error';

       $response->write(json_encode($message));

        return $response
        ->withHeader('Content-type','application/json')
        ->withStatus(422);
    }

});

$app->post('/userlogin',function(Request $request, Response $response){
    
    if(!haveEmptyParam(array('email','password'),$request,$response)){
        $request_data = $request->getParsedBody(); 
        $email = $request_data['email'];
        $password  =$request_data['password'];
 
        $db = new DbOperations;

        $result = $db->userLogin($email,$password);

        if($result==USER_AUTHENTICATED){

            $user = $db ->getUserByEmail($email);
            $response_data = array();
            $response_data['error'] = false;
            $response_data['message'] = 'Login Success';
            $response_data['user'] = $user;
            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type','application/json')
            ->withStatus(200);

        }else if ($result == USER_NOT_FOUND){
            $response_data = array();
            $response_data['error'] = true;
            $response_data['message'] = 'User  doesn\'t exist';
            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type','application/json')
            ->withStatus(404);


        }else if ($result == USER_PASS_INCORRECT){
            $response_data = array();
            $response_data['error'] = true;
            $response_data['message'] = 'Password Incorrect';
            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type','application/json')
            ->withStatus(404);

        }
    }
    
    return $response
    ->withHeader('Content-type','application/json')
    ->withStatus(422);
});


$app->get('/allusers',function(Request $request,Response $response){

    $db = new DbOperations;

    $users = $db->getAllUsers();

    $response_data ['error'] = false;
    $response_data ['users'] = $users;

    $response->write(json_encode($response_data));
    
    return $response
    ->withHeader('Content-type','application/json')
    ->withStatus(200);

});

$app->put('/updateinfo',function(Request $request, Response $response){

    if(!haveEmptyParam(array('email','password','new_password'),$request,$response)){
        $request_data = $request->getParsedBody(); 
        $email = $request_data['email'];
        $password  =$request_data['password'];
        $new_password = $request_data['new_password'];

        $response_data =  array();
        $db = new DbOperations;
        $result = $db->updateUserInfo($email,$password,$new_password);

            if($result == INFO_UPDATE_SUCCESS){
                $response_data['error'] = false;
                $response_data['message'] = 'Info updated successfully';
                
                $response->write(json_encode($response_data));

                return $response
                ->withHeader('Content-type','application/json')
                ->withStatus(200);


            }else if ($result == INFO_UPDATE_FAIL){
                $response_data['error'] = true;
                $response_data['message'] = 'Info updation failed';
                
                $response->write(json_encode($response_data));

                return $response
                ->withHeader('Content-type','application/json')
                ->withStatus(422);
            }else if ($result == USER_PASS_INCORRECT){
                $response_data['error'] = true;
                $response_data['message'] = 'Password Incorrect';
                
                $response->write(json_encode($response_data));

                return $response
                ->withHeader('Content-type','application/json')
                ->withStatus(422);
            }else if ($result == USER_NOT_FOUND){
                $response_data['error'] = true;
                $response_data['message'] = 'User not found';
                
                $response->write(json_encode($response_data));

                return $response
                ->withHeader('Content-type','application/json')
                ->withStatus(422);
            }

        }

    

    return $response
    ->withHeader('Content-type','application/json')
    ->withStatus(422);

});

$app->delete('/deleteuser',function(Request $request, Response $response){

    if(!haveEmptyParam(array('email','password'),$request,$response)){
        $request_data = $request->getParsedBody();
         
        $email = $request_data['email'];
        $password = $request_data['password'];

        $db = new DbOperations;

        $result = $db->deleteUser($email,$password);

        
        if($result == ACCOUNT_DELETED_SUCCESS){
            $response_data['error'] = false;
            $response_data['message'] = 'User deleted successfully';
            
            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type','application/json')
            ->withStatus(200);


        }else if ($result == ACCOUNT_DELETED_FAILURE){
            $response_data['error'] = true;
            $response_data['message'] = 'User detetion failed';
            
            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type','application/json')
            ->withStatus(422);
        }else if ($result == USER_PASS_INCORRECT){
            $response_data['error'] = true;
            $response_data['message'] = 'Password Incorrect';
            
            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type','application/json')
            ->withStatus(422);
        }else if ($result == USER_NOT_FOUND){
            $response_data['error'] = true;
            $response_data['message'] = 'User not found';
            
            $response->write(json_encode($response_data));

            return $response
            ->withHeader('Content-type','application/json')
            ->withStatus(422);
        }

    }

return $response
->withHeader('Content-type','application/json')
->withStatus(422);
    

});

function haveEmptyParam($require_param,$request, $response){

    $error = false;
    $error_param='';
    $request_param=$request->getParsedBody();

  foreach ($require_param as $param) {
    if(!isset($request_param[$param]) || strlen($request_param[$param])<=0){    
        $error = true;
        $error_param  = $error_param . $param .  ', ' ;
       }

  }
    if($error){
        $error_detail = array();
        $error_detail['error']= true;
        $error_detail['message']= 'Required parameters ' . substr($error_param,0,-2) . ' are missing';
        $response->write(json_encode($error_detail));
    }

    return $error;
}

$app->run();