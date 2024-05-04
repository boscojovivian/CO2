<!DOCTYPE html>
<html>
    <head>
        <title>註冊</title>
        <meta charset="utf-8"></meta>
        <link rel="stylesheet" href="css.css" type="text/css">
        <link rel="shortcut icon" href="img\logo.png" >
        <script src="js.js"></script>
    </head>

    <body class="signup_body">
        <div class="signup">
            <h1>註冊</h1>
            <form id="loginForm" method="post">

                <div class="signup_form">
                    <label for="name">姓名：</label>
                    <input type="text" id="name_up" name="name_up" required>

                    <label for="email">電子信箱：</label>
                    <input type="email" id="email_up" name="email_up" required>

                    <label for="password">密碼：</label>
                    <input type="password" id="password_up" name="password_up" required>

                    <label for="flag">您是否為管理員：</label>
                    <p><input type="radio" name="permission" class="radio" value="manage"> 是，我也有管理員權限</p>
                    <p><input type="radio" name="permission" class="radio" value="employee"> 否，我只有員工權限</p>
                </div>

                <br>
                
                <input type="submit" name="signup" data-style='submit1' value="註冊">
                <br>
                <a href="登入.php" class="back">已註冊，返回登入頁面</a>

                <br><br>
                     
                <?php
                    if (isset($_POST["signup"])) {
                        include_once('inc\Sign_up.inc');
                    }
                ?>
            </form>
        </div>

        <br>
    </body>
</html>    