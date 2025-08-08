<?php
session_start();
if (isset($_SESSION['usuario'])) {
    header("Location: views/menu_principal.php");
} else {
    header("Location: views/login.php");
}
exit();
?>