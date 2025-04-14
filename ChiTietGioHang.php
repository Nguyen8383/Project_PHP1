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

// Xử lý cập nhật số lượng
if (isset($_POST['update_quantity'])) {
    $productId = $_POST['product_id'];
    $quantity = max(1, (int)$_POST['quantity']); // Đảm bảo số lượng tối thiểu là 1
    
    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] = $quantity;
    }
    
    header("Location: ChiTietGioHang.php");
    exit();
}

$totalValue = 0;
$totalItems = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $totalValue += $item['product']['price'] * $item['quantity'];
        $totalItems += $item['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Giỏ Hàng</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .cart-header {
            background-color: #f8f9fa;
            padding: 20px 0;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .product-img {
            width: 80px;
            height: 80px;
            object-fit: cover;
            border-radius: 8px;
        }
        
        .cart-item {
            transition: all 0.3s ease;
        }
        
        .cart-item:hover {
            background-color: #f8f9fa;
        }
        
        .quantity-control {
            width: 120px;
        }
        
        .btn-circle {
            width: 30px;
            height: 30px;
            padding: 0;
            border-radius: 50%;
            text-align: center;
            line-height: 1;
        }
        
        .empty-cart {
            padding: 60px 0;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: #0d6efd;
        }
        
        .summary-card {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .checkout-btn {
            padding: 12px 30px;
            border-radius: 50px;
            font-weight: bold;
            transition: all 0.3s ease;
        }
        
        .checkout-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
        }
        
        .welcome-user {
            font-weight: 600;
        }
        
        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .product-info {
                text-align: center;
            }
            
            .product-img {
                margin-bottom: 10px;
            }
            
            .quantity-control {
                margin: 0 auto;
                margin-top: 10px;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-light bg-white shadow-sm sticky-top">
        <div class="container">
            <a class="navbar-brand" href="DanhSachSanPham.php">
                <i class="fas fa-store me-2"></i>Shop Online
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="DanhSachSanPham.php">
                            <i class="fas fa-th-list me-1"></i> Sản phẩm
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="ChiTietGioHang.php">
                            <i class="fas fa-shopping-cart me-1"></i> Giỏ hàng
                            <span class="badge bg-danger rounded-pill"><?= $totalItems ?></span>
                        </a>
                    </li>
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle welcome-user" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user-circle me-1"></i> <?= $_SESSION['user_id'] ?>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="fas fa-user me-2"></i>Tài khoản</a></li>
                            <li><a class="dropdown-item" href="#"><i class="fas fa-history me-2"></i>Lịch sử mua hàng</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li><a class="dropdown-item text-danger" href="DangXuat.php"><i class="fas fa-sign-out-alt me-2"></i>Đăng xuất</a></li>
                        </ul>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <!-- Cart Header -->
        <div class="cart-header text-center">
            <h1 class="display-6 mb-0"><i class="fas fa-shopping-cart me-3"></i>Giỏ Hàng Của Bạn</h1>
        </div>

        <?php if (!empty($_SESSION['cart'])): ?>
            <div class="row">
                <!-- Cart Items -->
                <div class="col-lg-8 mb-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            <h5 class="mb-0">Chi tiết sản phẩm (<?= $totalItems ?> sản phẩm)</h5>
                        </div>
                        <div class="card-body p-0">
                            <?php foreach ($_SESSION['cart'] as $itemId => $item): ?>
                                <div class="cart-item p-3 border-bottom">
                                    <div class="row align-items-center">
                                        <div class="col-md-2 col-4 text-center mb-2 mb-md-0">
                                            <!-- Giả định hình ảnh, thay thế bằng hình ảnh thực nếu có -->
                                            <img src="<?= isset($item['product']['image']) ? $item['product']['image'] : 'https://via.placeholder.com/80' ?>" 
                                                 alt="<?= $item['product']['name'] ?>" class="product-img">
                                        </div>
                                        <div class="col-md-4 col-8 product-info">
                                            <h5 class="mb-1"><?= $item['product']['name'] ?></h5>
                                            <p class="text-muted small mb-0">Mã SP: <?= $item['product']['id'] ?></p>
                                        </div>
                                        <div class="col-md-2 col-6 text-center text-md-start mt-3 mt-md-0">
                                            <span class="text-primary fw-bold"><?= number_format($item['product']['price'], 0, ',', '.') ?> VND</span>
                                        </div>
                                        <div class="col-md-2 col-6 text-center mt-3 mt-md-0">
                                            <form method="post" class="quantity-form">
                                                <div class="input-group quantity-control">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="decrementQuantity(this)">
                                                        <i class="fas fa-minus"></i>
                                                    </button>
                                                    <input type="number" name="quantity" class="form-control text-center" value="<?= $item['quantity'] ?>" min="1">
                                                    <button type="button" class="btn btn-outline-secondary btn-sm" onclick="incrementQuantity(this)">
                                                        <i class="fas fa-plus"></i>
                                                    </button>
                                                    <input type="hidden" name="product_id" value="<?= $itemId ?>">
                                                    <button type="submit" name="update_quantity" class="d-none">Cập nhật</button>
                                                </div>
                                            </form>
                                        </div>
                                        <div class="col-md-2 col-12 text-end mt-3 mt-md-0">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <span class="fw-bold d-md-none">Tổng:</span>
                                                <span class="text-success fw-bold"><?= number_format($item['product']['price'] * $item['quantity'], 0, ',', '.') ?> VND</span>
                                                <form method="post" class="ms-2">
                                                    <input type="hidden" name="product_id" value="<?= $itemId ?>">
                                                    <button type="submit" name="remove" class="btn btn-danger btn-circle">
                                                        <i class="fas fa-trash-alt"></i>
                                                    </button>
                                                </form>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                        <div class="card-footer bg-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="DanhSachSanPham.php" class="btn btn-outline-primary">
                                    <i class="fas fa-arrow-left me-2"></i>Tiếp tục mua sắm
                                </a>
                                <span class="text-muted">Cập nhật số lượng tự động khi thay đổi</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Order Summary -->
                <div class="col-lg-4">
                    <div class="summary-card sticky-top" style="top: 90px;">
                        <h5 class="mb-4">Tóm Tắt Đơn Hàng</h5>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tổng sản phẩm:</span>
                            <span><?= $totalItems ?></span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Tạm tính:</span>
                            <span><?= number_format($totalValue, 0, ',', '.') ?> VND</span>
                        </div>
                        
                        <div class="d-flex justify-content-between mb-2">
                            <span>Phí vận chuyển:</span>
                            <span>0 VND</span>
                        </div>
                        
                        <hr>
                        
                        <div class="d-flex justify-content-between mb-4">
                            <span class="fw-bold">Tổng cộng:</span>
                            <span class="fw-bold text-danger h5 mb-0"><?= number_format($totalValue, 0, ',', '.') ?> VND</span>
                        </div>
                        
                        <button class="btn btn-success w-100 checkout-btn">
                            <i class="fas fa-credit-card me-2"></i>Thanh Toán
                        </button>
                        
                        <div class="mt-4">
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-truck text-primary me-2"></i>
                                <span>Miễn phí vận chuyển cho đơn hàng trên 500.000đ</span>
                            </div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="fas fa-undo text-primary me-2"></i>
                                <span>Chính sách đổi trả trong vòng 30 ngày</span>
                            </div>
                            <div class="d-flex align-items-center">
                                <i class="fas fa-shield-alt text-primary me-2"></i>
                                <span>Bảo mật thông tin thanh toán</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php else: ?>
            <!-- Empty Cart -->
            <div class="row">
                <div class="col-12">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body empty-cart text-center">
                            <i class="fas fa-shopping-cart fa-4x text-muted mb-4"></i>
                            <h3>Giỏ hàng của bạn đang trống</h3>
                            <p class="text-muted mb-4">Có vẻ như bạn chưa thêm bất kỳ sản phẩm nào vào giỏ hàng.</p>
                            <a href="DanhSachSanPham.php" class="btn btn-primary btn-lg">
                                <i class="fas fa-store me-2"></i>Bắt đầu mua sắm
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-dark text-white py-4 mt-5">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5>Shop Online</h5>
                    <p class="text-muted">Mang đến trải nghiệm mua sắm tuyệt vời cho bạn.</p>
                </div>
                <div class="col-md-4 mb-3 mb-md-0">
                    <h5>Liên Hệ</h5>
                    <ul class="list-unstyled text-muted">
                        <li><i class="fas fa-map-marker-alt me-2"></i>123 Đường ABC, Quận XYZ</li>
                        <li><i class="fas fa-phone me-2"></i>(123) 456-7890</li>
                        <li><i class="fas fa-envelope me-2"></i>info@shoponline.com</li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h5>Theo Dõi</h5>
                    <div class="d-flex">
                        <a href="#" class="text-white me-3"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white me-3"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white"><i class="fab fa-youtube"></i></a>
                    </div>
                </div>
            </div>
            <hr>
            <div class="text-center">
                <small class="text-muted">© 2025 Shop Online. All rights reserved.</small>
            </div>
        </div>
    </footer>

    <!-- Bootstrap Bundle with Popper -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Function to increment quantity
        function incrementQuantity(button) {
            const input = button.parentNode.querySelector('input[type=number]');
            input.value = parseInt(input.value) + 1;
            submitQuantityForm(button);
        }
        
        // Function to decrement quantity
        function decrementQuantity(button) {
            const input = button.parentNode.querySelector('input[type=number]');
            if (parseInt(input.value) > 1) {
                input.value = parseInt(input.value) - 1;
                submitQuantityForm(button);
            }
        }
        
        // Function to submit the form
        function submitQuantityForm(element) {
            const form = element.closest('.quantity-form');
            const submitButton = form.querySelector('button[name=update_quantity]');
            submitButton.click();
        }
        
        // Add event listeners to quantity input
        document.querySelectorAll('input[name="quantity"]').forEach(input => {
            input.addEventListener('change', function() {
                const form = this.closest('.quantity-form');
                const submitButton = form.querySelector('button[name=update_quantity]');
                submitButton.click();
            });
        });
    </script>
</body>
</html>