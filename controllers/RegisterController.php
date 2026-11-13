<?php


class RegisterController
{
    public $conn;

    public function __construct()
    {
        $db = new DatabaseConnection();
        $this->conn = $db->conn;
    }

    // main registration function
    public function registration($fullname, $matric_number, $hashed_password)
    {

        $created_at = date('Y-m-d H:i:s');
        $register_query = "INSERT INTO users (fullname, matric_number, role, password, created_at) VALUES ('$fullname', '$matric_number', 'student', '$hashed_password', '$created_at')";
        $result = $this->conn->query($register_query);
        return $result;
    }

    // check if user already exists
    public function checkUserExists($matric_number)
    {
        $query = "SELECT matric_number FROM users WHERE matric_number = '$matric_number'";
        $result = $this->conn->query($query);

        if ($result->num_rows > 0) {
            return true;
        } else {
            return false;
        }
    }

    // function to confirm password
    public function confirmPassword($password, $confirm_password)
    {
        if ($password == $confirm_password) {
            return true;
        } else {
            return false;
        }
    }


}

?>