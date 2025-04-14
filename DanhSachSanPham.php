<?php
session_start();

// Kiểm tra xem người dùng đã đăng nhập chưa
if (!isset($_SESSION['user_id'])) {
    header("Location: DangNhap.php");
    exit();
}

// Đọc dữ liệu từ file danhsachsanpham.txt
$products = [];
$file = fopen("danhsachsanpham.txt", "r");
while (($line = fgets($file)) !== false) {
    $data = explode("|", trim($line));
    $products[] = [
        'id' => $data[0],
        'name' => $data[1],
        'type' => $data[2],
        'price' => $data[3],
        'quantity' => $data[4],
        'image' => $data[5]
    ];
}
fclose($file);

// Xử lý thêm sản phẩm vào giỏ hàng
if (isset($_POST['add_to_cart'])) {
    $productId = $_POST['product_id'];
    if (!isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId] = ['product' => $products[$productId - 1], 'quantity' => 1];
    } else {
        $_SESSION['cart'][$productId]['quantity']++;
    }
    header("Location: DanhSachSanPham.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Sản Phẩm</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light">
        <a class="navbar-brand" href="#">Quản Lý Sản Phẩm</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ml-auto">
                <li class="nav-item">
                    <span class="nav-link">Xin chào, <?= $_SESSION['user_id'] ?></span>
                </li>
                <li class="nav-item">
                    <a href="ChiTietGioHang.php" class="nav-link">
                        <i class="fas fa-shopping-cart"></i> Giỏ Hàng
                        <span class="badge badge-pill badge-danger"><?= isset($_SESSION['cart']) ? count($_SESSION['cart']) : 0 ?></span>
                    </a>
                </li>
                <li class="nav-item">
                    <a href="DangXuat.php" class="nav-link">Đăng xuất</a>
                </li>
            </ul>
        </div>
    </nav>

    <div class="container mt-5">
        <h2 class="text-center mb-4">Danh Sách Sản Phẩm</h2>
        <table class="table table-bordered table-striped">
            <thead class="thead-dark">
                <tr>
                    <th>ID</th>
                    <th>Tên sản phẩm</th>
                    <th>Loại sản phẩm</th>
                    <th>Đơn giá</th>
                    <th>Số lượng</th>
                    <th>Hình ảnh</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($products as $product): ?>
                    <tr>
                        <td><?= $product['id'] ?></td>
                        <td><?= $product['name'] ?></td>
                        <td><?= $product['type'] ?></td>
                        <td><?= number_format($product['price'], 0, ',', '.') ?> VND</td>
                        <td><?= $product['quantity'] ?></td>
                        <td><img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>" class="img-thumbnail" style="width: 100px;"></td>
                        <td>
                            <form method="post">
                                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                <button type="submit" name="add_to_cart" class="btn btn-primary">Thêm vào giỏ hàng</button>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <script src="https://kit.fontawesome.com/a076d05399.js"></script>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>