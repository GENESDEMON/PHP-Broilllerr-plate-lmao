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

