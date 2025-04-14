<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (isset($_SESSION['user_id'])) {
    header("Location: DanhSachSanPham.php");
    exit();
}

$error = "";
$success = "";

// Xử lý khi form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    
    // Kiểm tra các trường không được để trống
    if (empty($username) || empty($password) || empty($confirm_password)) {
        $error = "Vui lòng điền đầy đủ thông tin.";
    } 
    // Kiểm tra mật khẩu và xác nhận mật khẩu trùng khớp
    elseif ($password !== $confirm_password) {
        $error = "Mật khẩu và xác nhận mật khẩu không khớp.";
    } 
    else {
        // Kiểm tra username đã tồn tại chưa
        $userExists = false;
        if (file_exists("taikhoan.txt")) {
            $file = fopen("taikhoan.txt", "r");
            while (($line = fgets($file)) !== false) {
                $data = explode("|", trim($line));
                if ($data[0] === $username) {
                    $userExists = true;
                    break;
                }
            }
            fclose($file);
        }
        
        if ($userExists) {
            $error = "Tên đăng nhập đã tồn tại, vui lòng chọn tên khác.";
        } else {
            // Mã hóa mật khẩu
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            // Ghi thông tin đăng ký vào file
            $file = fopen("taikhoan.txt", "a");
            fwrite($file, $username . "|" . $hashed_password . "\n");
            fclose($file);
            
            $success = "Đăng ký thành công. Bạn có thể đăng nhập ngay bây giờ.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Ký</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-primary text-white text-center">
                        <h4>Đăng Ký Tài Khoản</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
                        <?php endif; ?>
                        
                        <?php if (!empty($success)): ?>
                            <div class="alert alert-success"><?= $success ?></div>
                        <?php endif; ?>
                        
                        <form method="post" action="<?= $_SERVER['PHP_SELF'] ?>">
                            <div class="form-group">
                                <label for="username">Tên đăng nhập</label>
                                <input type="text" class="form-control" id="username" name="username" required>
                            </div>
                            <div class="form-group">
                                <label for="password">Mật khẩu</label>
                                <input type="password" class="form-control" id="password" name="password" required>
                            </div>
                            <div class="form-group">
                                <label for="confirm_password">Xác nhận mật khẩu</label>
                                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-block">Đăng Ký</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p class="mb-0">Đã có tài khoản? <a href="DangNhap.php">Đăng nhập</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>