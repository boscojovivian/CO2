<?php 
session_start();

if (isset($_SESSION['em_id'])) 
{
    unset($_SESSION['em_id']);
    unset($_SESSION['em_name']);
    unset($_SESSION['em_account']);
    unset($_SESSION['em_psd']);
    unset($_SESSION['em_email']);
    unset($_SESSION['flag']);
}

?>

<script>alert('登出成功');</script>
<script>window.location.href = 'Sign_in.php';</script>
