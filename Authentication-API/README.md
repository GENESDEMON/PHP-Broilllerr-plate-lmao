# This project contains the following features
- Registeration - creating a user account 
- Email confrimation - having the users validate their emails before granting them access to your application
- Login - allowing registered and validated emails have access to your applicationand generating a JWT when login is successful
- Update - create a user profile and update it aswell
- Verify is a JWT is still valid
- Forgot Password
- Add profile picture


## Starting this project, you will need?
- XAMPP installed on your PC
- POSTMAN to test all endpoints

## Packages used in this project

## Project folders

## Setting up your database
- Open your PhpMyAdmin localhost/phpmyadmin
- Create a new database (The database name here is api)
- Create a table USERS 
- Create fields - id, name, email, hobby, token, isVerified, forget_pass_identity, password, created, modified
- Do not forget to make id your primary key and add auto-increment as well.

```
 CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `name` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `hobby` varchar(200) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `isVerified` enum('true','false') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'false',
  `created` datetime NOT NULL DEFAULT current_timestamp(),
  `modified` timestamp NOT NULL DEFAULT current_timestamp(),
  `forgot_pass_identity` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

## Project set up
- If you're using XAMPP, you must create it inside the htdocs folder. 
- Create a folder call AUTH-API. (or whatever you wish)
- Move into your project folder and create a **config** 
- Then create a file **db.php**
- This file basically conects your php code to your database so you should always put your correct server details
- it should contain the code below:

```
<?php
class Database{
// put your own details 
    private $host = "localhost";
    private $dbname = "api"; // the database you created earlier
    private $username = "root";  // your username
    private $password = "";      //password
    public $conn;
    public function getConnection(){
        $this->conn = null;
     try{
            $this->conn = new PDO("mysql:host=" . $this->host . ";dbname=" . $this->dbname, $this->username, $this->password);
        }catch(PDOException $exception){
            echo "Connection error: " . $exception->getMessage();
        }
        return $this->conn;
    }
}
?>

```

## Creating a new user (Registration endpoint)
- In your project folder cretae a new file **register.php**
- This is the endpoint needed to create new users and add them to your database

```
\\ Headers. This endpoint will only be able to accept JSON data
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
```
 
 ## Create a User class
 - The above code is not going to work without this class
 - Remeber this line  **include_once 'objects/user.php';** it isn't going to work without creating this class
 - Let's create this class!
 - Cretae a folder **objects** in your project directory
 - Create a file in it and nme it **user.php**
 - The file will contain the code below

```
<?php
class User{
	private $conn;
    private $table_name = "users"; // this connects to the table we created in our database
	public $id;
	public $name;
	public $password;
	

	public function __construct($db){
		$this->conn = $db;
	}

// create() method for creating a new user
function create(){
	$query = "INSERT INTO " . $this->table_name . "
            SET
				name = :name,
				email = :email,
                password = :password";

	$stmt = $this->conn->prepare($query);
	$this->name = strtolower(htmlspecialchars(strip_tags($this->name)));
	$this->email=htmlspecialchars(strip_tags($this->email));
    $this->password=htmlspecialchars(strip_tags($this->password));

	$stmt->bindParam(':name', $this->name);
	$stmt->bindParam(':email', $this->email);
	$password_hash = password_hash($this->password, PASSWORD_BCRYPT); //encrypts the password before sending to the database, hence keeping it safe
    $stmt->bindParam(':password', $password_hash);
	if($stmt->execute()){
		return true;
	}
	return false;
}

// emailExists() method for login and registration. This checks if there is a user in the database with the eneterd email address
function emailExists(){
	$query = "SELECT id, name,  password
			FROM " . $this->table_name . "	WHERE email = ?
            LIMIT 0,1";
            
	$stmt = $this->conn->prepare( $query );
	$this->email=htmlspecialchars(strip_tags($this->email));
	$stmt->bindParam(1, $this->email);
	$stmt->execute();
	$num = $stmt->rowCount();

	// if email exists, assign values to object properties for easy access and use for php sessions
	if($num>0){
		$row = $stmt->fetch(PDO::FETCH_ASSOC);
		$this->id = $row['id'];
		$this->name = $row['name'];
        $this->password = $row['password'];
		return true;
	}
	return false;
}

}
?>

```
Screenshots
![alt text](https://github.com/[genesdemon]/[PHP-Broilllerr-plate-lmao]/blob/[master]/[images]/img1.jpg?raw=true)