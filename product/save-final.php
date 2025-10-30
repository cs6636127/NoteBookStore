<?php
session_start();
if (isset($_POST['finalprice'])) {
    $_SESSION["finalPrice"] = floatval($_POST['finalprice']);
}
?>
