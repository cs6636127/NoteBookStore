<?php
session_start();
include dirname(__DIR__) . "/navbar.php";
$params = session_get_cookie_params();
setcookie(
    session_name(),
    '',
    time() - 42000,
    $params["path"],
    $params["domain"],
    $params["secure"],
    $params["httponly"]
);

session_destroy(); // ทำลาย session
header("Location: ../product/product.php");
