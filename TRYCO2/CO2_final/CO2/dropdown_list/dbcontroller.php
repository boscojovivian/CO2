<?php
class DBController{     //新增一個類別DBController
    private $host = "localhost";
    private $user = "root";
    private $pass = "";
    private $dbname = "carbon_emissions";

    private $conn;

    function __construct(){     //在類別中建立建構子
        //$this的意思就是本身，在$this中有一個指標，誰呼叫他，他肘向誰，只能在類別內部使用
        //使用$this指向connectDB函式，將connectDB回傳資料存入conn
        $this->conn = $this->connectDB();
    }

    function connectDB(){       //在類別中定義成員connectDB
        $con = mysqli_connect($this->host, $this->user, $this->pass, $this->dbname);     //定義con變數儲存資料庫連接狀況
        mysqli_set_charset($con,"utf8");
        return $con;
    }

    function runQuery($query){
        $result = mysqli_query($this->conn, $query);
        while ($row = mysqli_fetch_assoc($result)){
            $resultset[] = $row;    //將讀取的所有資料存入resultset
        }
        if (!empty($resultset)){
            return $resultset;
        }
    }

    function runRows($query){
        $result = mysqli_query($this->conn, $query);
        $rowcount = mysqli_num_rows($result);
        return $rowcount;
    }

    // 用於插入數據
    function insert($table, $data) {
        $columns = implode(", ", array_keys($data));
        $values  = implode(", ", array_map(function($value) {
            return "'" . mysqli_real_escape_string($this->conn, $value) . "'";
        }, array_values($data)));

        $query = "INSERT INTO $table ($columns) VALUES ($values)";
        return $this->executeUpdate($query);
    }

    // 新增的select方法，用於取得指定條件的資料
    function select($table, $columns = "*", $conditions = []) {
        $conditionString = "";
        if (!empty($conditions)) {
            $conditionArray = [];
            foreach ($conditions as $column => $value) {
                $conditionArray[] = "$column = '" . mysqli_real_escape_string($this->conn, $value) . "'";
            }
            $conditionString = "WHERE " . implode(" AND ", $conditionArray);
        }

        $query = "SELECT $columns FROM $table $conditionString";
        $result = mysqli_query($this->conn, $query);

        $resultset = [];
        while ($row = mysqli_fetch_assoc($result)) {
            $resultset[] = $row;
        }

        return !empty($resultset) ? $resultset : null;
    }

    // 用於取得最後插入的 ID
    function getLastInsertId() {
        return mysqli_insert_id($this->conn);
    }

    function executeUpdate($query){
        $result = mysqli_query($this->conn, $query);
        return $result;
    }
    
    function updateUserPermission($table, $data, $conditions) {
        $updateValues = [];
        foreach ($data as $column => $value) {
            $updateValues[] = "$column = '" . mysqli_real_escape_string($this->conn, $value) . "'";
        }
        $updateString = implode(", ", $updateValues);

        $conditionString = implode(" AND ", array_map(function($column, $value) {
            return "$column = '" . mysqli_real_escape_string($this->conn, $value) . "'";
        }, array_keys($conditions), $conditions));

        $query = "UPDATE $table SET $updateString WHERE $conditionString";
        return $this->executeUpdate($query);
    }

    // 關閉資料庫連線
    function close() {
        if ($this->conn) {
            mysqli_close($this->conn);
        }
    }
    public function getConnection() {
        return $this->conn;
    }
}


?>