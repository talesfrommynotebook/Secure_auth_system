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
    public function registration($fullname, $matric_number, $password, $hashed_password)
    {
        // make sure matrci number follows the correct format, e.g., CSC/DE/25/001
        // Supports 2-4 letter department codes, an optional middle category (like /DE), 2-digit for year, and 3-to-4 digit serial numbers
        $pattern = '/^[A-Z]{2,4}(\/[A-Z]{2})?\/\d{2}\/\d{3,4}$/';
        if (!preg_match($pattern, $matric_number)) {
            redirect("Invalid matric number format. Please use the format: CSC/DE/12/001", "red", site_url("register.php"));
            exit();
        }

        // password must be at least 8 characters long
        if (strlen($password) < 8) {
            redirect("Password must be at least 8 characters long", "red", site_url("register.php"));
            exit();
        }

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