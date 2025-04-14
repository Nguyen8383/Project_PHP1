<?php
session_start();
if (isset($_SESSION['user_id'])) {
    header("Location: DanhSachSanPham.php");
    exit();
}
header("Location: DangNhap.php");
exit();
?>