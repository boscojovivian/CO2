<?php
session_start();
// 檢查用戶是否已登入
if (!isset($_SESSION['em_id'])) {
    // 如果未登入，重定向到登入頁面
    header("Location: Sign_in.php");
    exit();
}
?>

<!doctype html>
<html lang="en">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>員工資料</title>
        <link rel="stylesheet" href="./css/employee.css" type="text/css">
        <link rel="shortcut icon" href="img/logo.png" >
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-rbsA2VBKQhggwzxH7pPCaAqO46MgnOM80zW1RWuH61DGLwZJEdK2Kadq2F9CUG65" crossorigin="anonymous">
    </head>
    <body>
        <!-- 導覽列 -->
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container-fluid">
                <a class="navbar-brand" href="cm_index.php"><img class="logo" src="img/logo.png" alt=""></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="#">管理者首頁</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">員工資料</a>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                交通車資料
                            </a>
                            <ul class="dropdown-menu dropdown-menu-custom">
                                <li><a class="dropdown-item" href="#">管理交通車</a></li>
                            </ul>
                        </li>
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                碳排紀錄
                            </a>
                            <ul class="dropdown-menu dropdown-menu-custom">
                                <li><a class="dropdown-item" href="#">交通車碳排紀錄</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="#">員工碳排紀錄</a></li>
                            </ul>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">回報問題</a>
                        </li>

                        <?php
                        if (isset($_SESSION['em_name'])) {
                            $user_name = $_SESSION['em_name'];
                        ?>

                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle active" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <?php echo $user_name; ?> <!-- 顯示用戶名 -->
                            </a>
                            <ul class="dropdown-menu dropdown-menu-custom">
                                <li><a class="dropdown-item" href="em_index.php">員工首頁</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li><a class="dropdown-item" href="cm_index.php">管理者首頁</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form method="post" class="dropdown-item"> <!-- 登出按鈕放在表單裡 -->
                                        <input type="submit" name="logout" value="登出" class="btn btn-success"> <!-- 使用btn-link樣式讓它看起來像連結 -->
                                    </form>
                                </li>
                            </ul>
                        </li>

                        <?php
                        } else {
                            // 若未登入顯示的項目
                            echo "<li><a class='nav-link'>未登入</a></li>";
                        }

                        // 登出功能
                        if (isset($_POST["logout"])) {
                            include_once('inc/log_out.inc');
                        }
                        ?>
                    </ul>
                </div>
            </div>
        </nav>

        <div class="container">
            <!-- 表格 -->
            <table class="table table-hover caption-top">
                <caption>員工資料</caption>
                <thead class="table">
                    <tr class="active">
                        <th>員工編號</th>
                        <th>姓名</th>
                        <th>電子信箱</th>
                        <th>管理員狀態</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                        include_once("dbcontroller.php");
                        $dbController = new DBController();
                        $query = "SELECT em_id, em_name, em_email, flag FROM employee";
                        $result = $dbController->runQuery($query);

                        if ($result) {
                            foreach ($result as $row) {
                                echo "<tr>";
                                echo "<td>".$row["em_id"]."</td>";
                                echo "<td>".$row["em_name"]."</td>";
                                echo "<td>".$row["em_email"]."</td>";
                                echo "<td>";
                                
                                if ($row["em_id"] != 1) {
                                    if ($row["flag"] == 0) {
                                        echo "<button type='button' class='add-button btn btn-success' onclick='setAdmin(".$row["em_id"].")'>新增</button>";
                                    } elseif ($row["flag"] == 1) {
                                        echo "<button type='button' class='add-button btn btn-danger' onclick='deleteAdmin(".$row["em_id"].")'>刪除</button>";
                                    }
                                }
                                
                                echo "</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='4'>0 results</td></tr>";
                        }
                    ?>
                </tbody>
            </table>
        </div>

        

        

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
    </body>
</html>