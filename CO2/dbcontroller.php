<?php
class DBController{     //新增一個類別DBController
    private $host = "localhost";
    private $user = "root";
    private $pass = "A12345678";
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
}
?>