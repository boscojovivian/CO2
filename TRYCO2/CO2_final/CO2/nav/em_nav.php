<style>
    .logo{
        width: 100%;
        height: 80px;
    }

    .navbar-custom{
        font-size: 22px;
        font-weight: bolder;
        background-color: #c0d2cd;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.15);
    }

    .collapse{
        margin-right: 5%;
    }

    .nav-item{
        margin: 0 10px;
    }

    .nav-item.dropdown:hover .dropdown-menu{
        display: block;
        margin-top: 0; /* Optional: 控制選單與按鈕的間距 */
    }

    .logout_submit{
        background-color: none;
    }

    .dropdown-item{
        margin-bottom: 1%;
        font-size: 22px !important;
        font-weight: bold !important;
        color: rgba(0, 0, 0, 0.55) !important;
    }

    .dropdown-item:hover{
        background-color: #fff !important;
        color: black !important;
    }
</style>

<!-- 導覽列 -->
        <nav class="navbar navbar-expand-lg navbar-custom">
            <div class="container-fluid">
                <a class="navbar-brand" href="index.php"><img class="logo" src="img/logo.png" alt=""></a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavDropdown" aria-controls="navbarNavDropdown" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>
                <div class="collapse navbar-collapse justify-content-end" id="navbarNavDropdown">
                    <ul class="navbar-nav">
                        <li class="nav-item">
                            <a class="nav-link" aria-current="page" href="index.php">個人首頁</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="em_work.php">公司車出勤紀錄</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="CO2_class.php">碳排教室</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="em_feedback.php">回報問題</a>
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
                                <?php
                                    if($_SESSION['flag'] == 1){
                                        echo "<li><a class='dropdown-item' href='cm_index.php'>管理者首頁</a></li>";
                                        echo "<li><hr class='dropdown-divider'></li>";
                                    }
                                ?>
                                
                                
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