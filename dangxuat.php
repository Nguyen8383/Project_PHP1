<?php
session_start();

// Xóa tất cả các biến session
$_SESSION = array();

// Hủy phiên session
session_destroy();

// Chuyển hướng về trang đăng nhập
header("Location: DangNhap.php");
exit();
?>