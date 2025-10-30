<?php
session_start();
if (isset($_POST['promo'])) {
    $_SESSION['id_promo'] = $_POST['promo'];
}
?>