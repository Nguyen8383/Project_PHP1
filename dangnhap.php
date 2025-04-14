<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (isset($_SESSION['user_id'])) {
    header("Location: DanhSachSanPham.php");
    exit();
}

$error = "";

// Xử lý khi form được submit
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);
    
    // Kiểm tra các trường không được để trống
    if (empty($username) || empty($password)) {
        $error = "Vui lòng điền đầy đủ thông tin.";
    } else {
        // Kiểm tra tài khoản trong file
        $authenticated = false;
        if (file_exists("taikhoan.txt")) {
            $file = fopen("taikhoan.txt", "r");
            while (($line = fgets($file)) !== false) {
                $data = explode("|", trim($line));
                if ($data[0] === $username && password_verify($password, $data[1])) {
                    $authenticated = true;
                    break;
                }
            }
            fclose($file);
        }
        
        if ($authenticated) {
            // Đăng nhập thành công
            $_SESSION['user_id'] = $username;
            
            // Chuyển hướng đến trang danh sách sản phẩm
            header("Location: DanhSachSanPham.php");
            exit();
        } else {
            $error = "Tên đăng nhập hoặc mật khẩu không chính xác.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-header bg-success text-white text-center">
                        <h4>Đăng Nhập</h4>
                    </div>
                    <div class="card-body">
                        <?php if (!empty($error)): ?>
                            <div class="alert alert-danger"><?= $error ?></div>
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
                            <button type="submit" class="btn btn-success btn-block">Đăng Nhập</button>
                        </form>
                    </div>
                    <div class="card-footer text-center">
                        <p class="mb-0">Chưa có tài khoản? <a href="DangKy.php">Đăng ký</a></p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>