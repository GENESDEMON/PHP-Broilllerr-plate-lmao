<?php
header("Access-Control-Allow-Origin: http://localhost/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once 'config/db.php'; //connects us to the database
include_once 'objects/user.php'; // connects us to our object user
$database = new Database(); //gets the database connection
$db = $database->getConnection();
$user = new User($db); //this creates the object instance
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// assiginin values. Gets data from users and assign them to the different fields the api is expected to call and send to the database
$user->name = $data->name; 
$user->email = $data->email;
$user->password = $data->password;
$email_exists = $user->emailExists();

//regex to check password strength
//This condition checks the password format
$containsLetter  = preg_match('/[a-zA-Z]/',    $user->password); //checks if it contains letters
$containsDigit   = preg_match('/\d/',          $user->password); //checks if it digits letters
$containsSpecial = preg_match('/[^a-zA-Z\d]/', $user->password); //checks if it special characeter letters
 
// calling the create method from the user class
if($email_exists){ //check if a user already has that email address
    http_response_code(401);
    exit(json_encode(array("message" => "Email already exists")));	
}elseif (strlen($user->password) < 8) { 
    exit(json_encode(array("message" => "Password must be at least 8 characters")));	
}elseif (!$containsLetter or !$containsDigit or !$containsSpecial) {
    exit(json_encode(array("message" => "Password must contain letters, number and special characters")));	
} else {
if(
    !empty($user->name) &&
    !empty($user->email) &&
    !empty($user->password) &&
    $user->create()
){
 
    // set response code (This is what tells if the registration was successful or not)
    http_response_code(200);
    echo json_encode(array("message" => "User was created."));
}
else{
    http_response_code(400);
    echo json_encode(array("message" => "Ooops.User not."));
}}
?>