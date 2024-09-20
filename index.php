<?php
session_start();
if (!isset($_SESSION['role'])) {
    header("Location: login.php");
    exit;
}

if ($_SESSION['role'] == 'admin') {
    header("Location: views/admin/admin.php");
} else {
    header("Location: views/user/user.php");
}
?>
