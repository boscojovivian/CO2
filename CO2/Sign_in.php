<!DOCTYPE html>
<html>
    <head>
        <title>登入</title>
        <meta charset="utf-8"></meta>
        <link rel="stylesheet" href="css.css" type="text/css">
        <link rel="shortcut icon" href="img\logo.png" >
        <script src="js.js"></script>
    </head>

    <body class="login_body">
        <div class="login">
            <h1>登入</h1>
            <form id="loginForm" method="post">

                <div class="login_form">
                    <label for="email">電子信箱：</label>
                    <input type="email" id="email_in" name="email_in" required>

                    <label for="password">密碼：</label>
                    <input type="password" id="password_in" name="password_in" required>
                </div>

                <br><br>

                <input type="submit" name="login" data-style='submit1' value="登入">
                <br>
                <a href="Sign_up.php" class="back">未註冊，前往註冊頁面</a>

                <br><br>
                        
                <?php
                    if (isset($_POST["login"])) {
                        include_once('inc\Sign_in.inc');
                    }
                ?>
            </form>
        </div>
    </body>
</html>    