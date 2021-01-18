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

include_once 'config/database.php'; //connects us to the database
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
 
```
 
