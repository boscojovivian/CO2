<!DOCTYPE html>
<html>
    <head>
        <title>登入</title>
        <meta charset="utf-8"></meta>
        <link rel="stylesheet" href="sign_in_css.css" type="text/css">
        <link rel="shortcut icon" href="img\logo.png" >
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <script src="js.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.7.3/dist/sweetalert2.all.min.js"></script>
        <!-- 引入 Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
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

    <body class="bg-light">
        <div class="container vh-100">
            <div class="row h-100 justify-content-center align-items-center p-5">
                <div class="col-md-8 col-lg-6">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <div class="p-5">
                                <h1 class="text-center mb-4 fw-bold">登入</h1>
                                <form id="loginForm" method="post">

                                    <div class="mb-3">
                                        <label for="email" class="form-label">電子信箱：</label>
                                        <input type="email" class="form-control" id="email_in" name="email_in" required>
                                    </div>

                                    <div class="mb-3">
                                        <label for="password" class="form-label">密碼：</label>
                                        <input type="password" class="form-control" id="password_in" name="password_in" required>
                                    </div>

                                    <!-- 使按鈕水平置中 -->
                                    <div class="d-flex justify-content-center mt-5">
                                        <button type="submit" name="login" class="btn sign-in-btn text-white p-3 w-75 fs-5">登入</button>
                                    </div>

                                    <!-- 
                                    <div class="text-center mt-3">
                                        <a href="Sign_up.php" class="text-secondary">未註冊？前往註冊頁面</a>
                                    </div>
                                    -->

                                    <?php
                                        if (isset($_POST["login"])) {
                                            include_once('inc/Sign_in.inc');
                                        }
                                    ?>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- 引入 Bootstrap JS -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    </body>
</html>    