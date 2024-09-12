<?php
class DBController {
    private $host = "localhost";
    private $user = "root";
    private $pass = "A12345678";
    private $dbname = "carbon_emissions";
    private $conn;

    function __construct() {
        $this->conn = $this->connectDB();
    }

    function connectDB() {
        $con = mysqli_connect($this->host, $this->user, $this->pass, $this->dbname);
        mysqli_set_charset($con, "utf8");
        if (mysqli_connect_errno()) {
            die("Failed to connect to MySQL: " . mysqli_connect_error());
        }
        return $con;
    }

    function runQuery($query, $param_type, $param_value_array) {
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die("Error preparing statement: " . $this->conn->error);
        }

        if (!empty($param_type) && !empty($param_value_array)) {
            $stmt->bind_param($param_type, ...$param_value_array);
        }

        $stmt->execute();
        $result = $stmt->get_result();

        if ($result === false) {
            die("Error executing query: " . $this->conn->error);
        }

        $resultset = array();
        while ($row = $result->fetch_assoc()) {
            $resultset[] = $row;
        }

        $stmt->close();

        return !empty($resultset) ? $resultset : null;
    }

    function updateQuery($query, $param_type, $param_value_array) {
        $stmt = $this->conn->prepare($query);
        if ($stmt === false) {
            die("Error preparing statement: " . $this->conn->error);
        }

        $stmt->bind_param($param_type, ...$param_value_array);
        $stmt->execute();

        if ($stmt->affected_rows > 0) {
            $stmt->close();
            return true;
        } else {
            $stmt->close();
            return false;
        }
    }
}
?>

