<!DOCTYPE html>
<html>
    <head>
        <title>登入</title>
        <meta charset="utf-8"></meta>
        <link rel="stylesheet" href="css.css" type="text/css">
        <link rel="shortcut icon" href="img\logo.png" >
        <script src="js.js"></script>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
        <script>
            const showAlert_logo = (title) => {
                Swal.fire({
                    title: title,
                    imageUrl: 'img/logo.png',
                    imageWidth: 150,
                    imageHeight: 100,
                    imageAlt: 'Custom image'
                }).then(() => {
                    window.location.href = 'em_index.php';
                });
            }
            const showAlert_success = (title) => {
                Swal.fire({
                    title: title,
                    icon: 'success'
                }).then(() => {
                    window.location.href = 'em_index.php';
                });
            }
            const showAlert_fail = (title) => {
                Swal.fire({
                    title: title,
                    icon: 'error'
                })
            }
        </script>
    </head>

    <body class="login_body">
        <div class="login">
        <div class="container-fluid p-0">
        <div class="row g-0">
            
        <div class="col-lg-7 d-flex align-items-center justify-content-center">
                <div class="login-container w-75">
                    <div class="text-center w-100">
                    <img src="img\logo.png" alt="logo" class="logo" >
            <form id="loginForm" method="post">

                            <div class="input-group mb-3">
                                 <span class="input-group-text" id="basic-addon1">gmail圖</span>
                                 <input type="text" id="email_in"  class="form-control" name="email_in" placeholder="請輸入電子郵件" required>    
                                
                            </div>  

                             <div class="input-group mb-3">
                                 <span class="input-group-text" id="basic-addon2">***</span>
                                 <input type="password" id="password_in" class="form-control"name="password_in" placeholder="請輸入密碼" required>
                                
                            </div>
                <br><br>

                <input type="submit" class="btn btn-primary w-100" name="login" data-style='submit1'  value="登入">
               
                
                <!-- <br>
                <a href="Sign_up.php" class="back">未註冊，前往註冊頁面</a> -->

                <br><br>
                        
                <?php
                    if (isset($_POST["login"])) {
                        include_once('inc\Sign_in.inc');
                    }
                ?>
            </form>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js" integrity="sha384-YvpcrYf0tY3lHB60NNkmXc5s9fDVZLESaAA55NDzOxhy9GkcIdslK1eN7N6jIeHz" crossorigin="anonymous"></script>
    </body>
</html>    