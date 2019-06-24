<?php

class DbOperations{

    private $con;

    function __construct(){

        //connect to the database
        require_once dirname(__FILE__) . '/DbConnect.php';
        $dbConnect = new Dbconnect;
        $this->con =  $dbConnect->connect();

    }

    function createUser($email, $password){

        if(!$this->isEmailExist($email)){
            
        $stmt= $this->con->prepare("Insert into users (email, password) values (?, ?)");
        $stmt->bind_param('ss',$email,$password);
        if($stmt->execute()){

            return USER_CREATED;
        }else{
            return USER_FAILURE;
        }
        }else{
            return USER_EXIXTS;
        }
    }

    function userLogin($email,$password){

        if($this->isEmailExist($email)){
            $hash_password = $this->getUserPasswordByEmail($email);
            if(password_verify($password,$hash_password)){
                return USER_AUTHENTICATED;
            }else{
                return USER_PASS_INCORRECT;
            }

        }else{
            return USER_NOT_FOUND;
        }

    }

    public function getAllUsers(){
        $stmt = $this->con->prepare("select email,password from users");
        $stmt->execute();
        $stmt->bind_result($email,$password);

        $all_users = array();

         while($stmt->fetch()){
        $user = array();
        $user['email']=$email;
        $user['password']=$password;
        array_push($all_users,$user);
    }
    return $all_users;
    }
    
    public function getUserByEmail($email){
        $stmt = $this->con->prepare("select email,password from users where email = ?");
        $stmt->bind_param('s',$email);
        $stmt->execute();
        $stmt->bind_result($email,$password);
        $stmt->fetch();
        $user = array();
        $user['email']=$email;
        $user['password']=$password;
        return $user;
    }
    private function getUserPasswordByEmail($email){
        $stmt = $this->con->prepare("select password from users where email = ?");
        $stmt->bind_param('s',$email);
        $stmt->execute();
        $stmt->bind_result($password);
        $stmt->fetch();
        return $password;
    }


    function isEmailExist($email){
        $stmt = $this->con->prepare("select * from users where email = ?");
        $stmt->bind_param('s',$email);
        $stmt->execute();
        $stmt->store_result();
        return $stmt->num_rows > 0;
    }
}