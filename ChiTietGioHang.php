<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: DangNhap.php");
    exit();
}

if (isset($_POST['remove'])) {
    $productId = $_POST['product_id'];
    unset($_SESSION['cart'][$productId]);
    header("Location: ChiTietGioHang.php");
    exit();
}

$totalValue = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $totalValue += $item['product']['price'] * $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="DanhSachSanPham.php">Quản Lý Sản Phẩm</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <span class="nav-link">Xin chào, <?= $_SESSION['user_id'] ?></span>
                </li>
                <li class="nav-item">
                    <a href="DanhSachSanPham.php" class="nav-link">Danh sách sản phẩm</a>
                </li>
                <li class="nav-item">
                    <a href="DangXuat.php" class="nav-link">Đăng xuất</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Giỏ Hàng Của Bạn</h2>

        <?php if (!empty($_SESSION['cart'])): ?>
            <table class="table table-bordered table-striped">
                <thead class="thead-dark">
                    <tr>
                        <th>ID</th>
                        <th>Tên sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>Số lượng</th>
                        <th>Tổng giá</th>
                        <th>Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($_SESSION['cart'] as $itemId => $item): ?>
                        <tr>
                            <td><?= $item['product']['id'] ?></td>
                            <td><?= $item['product']['name'] ?></td>
                            <td><?= number_format($item['product']['price'], 0, ',', '.') ?> VND</td>
                            <td><?= $item['quantity'] ?></td>
                            <td><?= number_format($item['product']['price'] * $item['quantity'], 0, ',', '.') ?> VND</td>
                            <td>
                                <form method="post">
                                    <input type="hidden" name="product_id" value="<?= $itemId ?>">
                                    <button type="submit" name="remove" class="btn btn-danger btn-sm">Xóa</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <div class="d-flex justify-content-end">
                <h4>Tổng giá trị: <span class="text-danger"><?= number_format($totalValue, 0, ',', '.') ?> VND</span></h4>
            </div>

            <div class="d-flex justify-content-end mt-3">
                <button class="btn btn-success">Thanh Toán</button>
            </div>
        <?php else: ?>
            <p class="text-center">Giỏ hàng trống.</p>
            <div class="text-center mt-3">
                <a href="DanhSachSanPham.php" class="btn btn-primary">Tiếp tục mua sắm</a>
            </div>
        <?php endif; ?>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>