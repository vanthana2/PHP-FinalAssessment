<?php
class User {
    private $connection;
    private $table = "users";

    public function __construct($db) {
        $this->connection = $db;
    }

   
    public function register($username, $email, $password) {
        $query = "INSERT INTO " . $this->table . " (username, email, password) VALUES (:username, :email, :password)";
        $stmt = $this->connection->prepare($query);

        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':email', $email);
        $stmt->bindParam(':password', password_hash($password, PASSWORD_BCRYPT));
        

        return $stmt->execute();
    }

    public function login($usernameOrEmail, $password) {
        $query = "SELECT * FROM " . $this->table . " WHERE username = :usernameOrEmail OR email = :usernameOrEmail LIMIT 1";
        $stmt = $this->connection->prepare($query);
        $stmt->bindParam(':usernameOrEmail', $usernameOrEmail);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($user && password_verify($password, $user['password'])) {
            return $user;
        }
        return false;
    }

   
   
    
  
} 
?>
