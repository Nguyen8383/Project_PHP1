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
    
    // Thêm thông báo thành công
    $_SESSION['success_message'] = "Đã thêm sản phẩm vào giỏ hàng!";
    header("Location: DanhSachSanPham.php");
    exit();
}

// Tính tổng số sản phẩm trong giỏ hàng
$totalItems = 0;
if (isset($_SESSION['cart'])) {
    foreach ($_SESSION['cart'] as $item) {
        $totalItems += $item['quantity'];
    }
}

// Lọc sản phẩm theo loại (nếu có)
$productTypes = array_unique(array_column($products, 'type'));
$filteredProducts = $products;

if (isset($_GET['type']) && $_GET['type'] != 'all') {
    $filteredProducts = array_filter($products, function($product) {
        return $product['type'] == $_GET['type'];
    });
}

// Sắp xếp sản phẩm (nếu có)
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_asc':
            usort($filteredProducts, function($a, $b) {
                return $a['price'] - $b['price'];
            });
            break;
        case 'price_desc':
            usort($filteredProducts, function($a, $b) {
                return $b['price'] - $a['price'];
            });
            break;
        case 'name_asc':
            usort($filteredProducts, function($a, $b) {
                return strcmp($a['name'], $b['name']);
            });
            break;
        case 'name_desc':
            usort($filteredProducts, function($a, $b) {
                return strcmp($b['name'], $a['name']);
            });
            break;
    }
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Danh Sách Sản Phẩm</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .navbar-brand {
            font-weight: bold;
            color: #0d6efd;
        }
        
        .welcome-user {
            font-weight: 600;
        }
        
        .page-header {
            background-color: #ffffff;
            padding: 20px 0;
            margin-bottom: 30px;
            border-radius: 10px;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
        }
        
        .product-card {
            height: 100%;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: none;
            border-radius: 10px;
            overflow: hidden;
        }
        
        .product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.1);
        }
        
        .product-img-container {
            height: 200px;
            overflow: hidden;
            position: relative;
        }
        
        .product-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }
        
        .product-card:hover .product-img {
            transform: scale(1.05);
        }
        
        .product-type-badge {
            position: absolute;
            top: 10px;
            right: 10px;
            z-index: 10;
        }
        
        .product-info {
            padding: 1.25rem;
        }
        
        .product-title {
            font-size: 1.1rem;
            font-weight: 600;
            margin-bottom: 8px;
            height: 2.5rem;
            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
        }
        
        .product-price {
            color: #dc3545;
            font-weight: bold;
            font-size: 1.2rem;
        }
        
        .product-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem 1.25rem;
            background-color: #f8f9fa;
            border-top: 1px solid rgba(0,0,0,.125);
        }
        
        .product-quantity {
            font-size: 0.9rem;
            color: #6c757d;
        }
        
        .add-to-cart-btn {
            border-radius: 50px;
            padding: 0.375rem 1rem;
            transition: all 0.3s ease;
        }
        
        .add-to-cart-btn:hover {
            transform: translateY(-2px);
        }
        
        .filter-card {
            border-radius: 10px;
            overflow: hidden;
            margin-bottom: 20px;
        }
        
        .toast-container {
            position: fixed;
            top: 80px;
            right: 20px;
            z-index: 1050;
        }
        
        .back-to-top {
            position: fixed;
            bottom: 20px;
            right: 20px;
            display: none;
            width: 45px;
            height: 45px;
            line-height: 45px;
            border-radius: 50%;
            background-color: #0d6efd;
            color: white;
            text-align: center;
            z-index: 1000;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .back-to-top:hover {
            background-color: #0b5ed7;
            transform: translateY(-3px);
        }
        
        /* Responsive adjustments */
        @media (max-width: 767.98px) {
            .product-card {
                margin-bottom: 20px;
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
                        <a class="nav-link active" href="DanhSachSanPham.php">
                            <i class="fas fa-th-list me-1"></i> Sản phẩm
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="ChiTietGioHang.php">
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

    <!-- Toast Notification -->
    <?php if (isset($_SESSION['success_message'])): ?>
    <div class="toast-container">
        <div class="toast show align-items-center text-white bg-success border-0" role="alert" aria-live="assertive" aria-atomic="true">
            <div class="d-flex">
                <div class="toast-body">
                    <i class="fas fa-check-circle me-2"></i><?= $_SESSION['success_message'] ?>
                </div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
            </div>
        </div>
    </div>
    <?php unset($_SESSION['success_message']); endif; ?>

    <div class="container py-5">
        <!-- Page Header -->
        <div class="page-header text-center mb-4">
            <h1 class="display-6 mb-0"><i class="fas fa-store me-3"></i>Danh Sách Sản Phẩm</h1>
        </div>

        <div class="row">
            <!-- Sidebar Filters -->
            <div class="col-lg-3 mb-4">
                <!-- Filter by Category -->
                <div class="card filter-card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-filter me-2"></i>Lọc Sản Phẩm</h5>
                    </div>
                    <div class="card-body">
                        <h6 class="text-muted mb-3">Loại sản phẩm</h6>
                        <div class="list-group">
                            <a href="?type=all" class="list-group-item list-group-item-action <?= (!isset($_GET['type']) || $_GET['type'] == 'all') ? 'active' : '' ?>">
                                Tất cả
                            </a>
                            <?php foreach ($productTypes as $type): ?>
                                <a href="?type=<?= $type ?>" class="list-group-item list-group-item-action <?= (isset($_GET['type']) && $_GET['type'] == $type) ? 'active' : '' ?>">
                                    <?= $type ?>
                                </a>
                            <?php endforeach; ?>
                        </div>
                    </div>
                </div>

                <!-- Sort Products -->
                <div class="card filter-card shadow-sm">
                    <div class="card-header bg-white">
                        <h5 class="mb-0"><i class="fas fa-sort me-2"></i>Sắp xếp</h5>
                    </div>
                    <div class="card-body">
                        <div class="list-group">
                            <a href="?<?= isset($_GET['type']) ? 'type=' . $_GET['type'] . '&' : '' ?>sort=price_asc" class="list-group-item list-group-item-action <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'active' : '' ?>">
                                <i class="fas fa-sort-amount-down-alt me-2"></i>Giá: Thấp đến cao
                            </a>
                            <a href="?<?= isset($_GET['type']) ? 'type=' . $_GET['type'] . '&' : '' ?>sort=price_desc" class="list-group-item list-group-item-action <?= (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'active' : '' ?>">
                                <i class="fas fa-sort-amount-up me-2"></i>Giá: Cao đến thấp
                            </a>
                            <a href="?<?= isset($_GET['type']) ? 'type=' . $_GET['type'] . '&' : '' ?>sort=name_asc" class="list-group-item list-group-item-action <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'active' : '' ?>">
                                <i class="fas fa-sort-alpha-down me-2"></i>Tên: A-Z
                            </a>
                            <a href="?<?= isset($_GET['type']) ? 'type=' . $_GET['type'] . '&' : '' ?>sort=name_desc" class="list-group-item list-group-item-action <?= (isset($_GET['sort']) && $_GET['sort'] == 'name_desc') ? 'active' : '' ?>">
                                <i class="fas fa-sort-alpha-up me-2"></i>Tên: Z-A
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Grid -->
            <div class="col-lg-9">
                <div class="bg-white p-3 rounded shadow-sm mb-4">
                    <div class="d-flex align-items-center justify-content-between">
                        <p class="mb-0">Hiển thị <strong><?= count($filteredProducts) ?></strong> sản phẩm</p>
                        <div class="btn-group">
                            <button type="button" class="btn btn-outline-secondary active" id="grid-view">
                                <i class="fas fa-th"></i>
                            </button>
                            <button type="button" class="btn btn-outline-secondary" id="list-view">
                                <i class="fas fa-list"></i>
                            </button>
                        </div>
                    </div>
                </div>

                <?php if (empty($filteredProducts)): ?>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>Không tìm thấy sản phẩm phù hợp với tiêu chí lọc.
                    </div>
                <?php else: ?>
                    <div class="row g-4" id="product-container">
                        <?php foreach ($filteredProducts as $product): ?>
                            <div class="col-md-4 col-sm-6 mb-4 product-item">
                                <div class="card product-card shadow-sm h-100">
                                    <div class="product-img-container">
                                        <span class="badge bg-primary product-type-badge"><?= $product['type'] ?></span>
                                        <img src="<?= $product['image'] ?>" alt="<?= $product['name'] ?>" class="product-img">
                                    </div>
                                    <div class="product-info">
                                        <h5 class="product-title"><?= $product['name'] ?></h5>
                                        <p class="product-price"><?= number_format($product['price'], 0, ',', '.') ?> VND</p>
                                        <div class="d-flex justify-content-between align-items-center">
                                            <span class="product-quantity">
                                                <?php if ($product['quantity'] > 0): ?>
                                                    <i class="fas fa-check-circle text-success me-1"></i>Còn hàng (<?= $product['quantity'] ?>)
                                                <?php else: ?>
                                                    <i class="fas fa-times-circle text-danger me-1"></i>Hết hàng
                                                <?php endif; ?>
                                            </span>
                                            <span class="text-muted small">Mã SP: <?= $product['id'] ?></span>
                                        </div>
                                    </div>
                                    <div class="product-footer">
                                        <form method="post" class="w-100">
                                            <input type="hidden" name="product_id" value="<?= $product['id'] ?>">
                                            <button type="submit" name="add_to_cart" class="btn btn-primary add-to-cart-btn w-100" <?= $product['quantity'] <= 0 ? 'disabled' : '' ?>>
                                                <i class="fas fa-shopping-cart me-2"></i>Thêm vào giỏ
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Back to top button -->
    <a href="#" class="back-to-top">
        <i class="fas fa-arrow-up"></i>
    </a>

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
        // Auto-hide toast after 3 seconds
        const toastElement = document.querySelector('.toast');
        if (toastElement) {
            setTimeout(() => {
                const toast = bootstrap.Toast.getInstance(toastElement);
                if (toast) {
                    toast.hide();
                }
            }, 3000);
        }
        
        // Back to top button
        window.onscroll = function() {
            if (document.body.scrollTop > 20 || document.documentElement.scrollTop > 20) {
                document.querySelector('.back-to-top').style.display = "block";
            } else {
                document.querySelector('.back-to-top').style.display = "none";
            }
        };
        
        // List/Grid view toggle
        document.getElementById('list-view').addEventListener('click', function() {
            document.getElementById('grid-view').classList.remove('active');
            this.classList.add('active');
            
            const container = document.getElementById('product-container');
            const items = document.querySelectorAll('.product-item');
            
            container.classList.add('list-view');
            items.forEach(item => {
                item.classList.remove('col-md-4', 'col-sm-6');
                item.classList.add('col-12', 'mb-3');
                
                // Restructure card for list view
                const card = item.querySelector('.product-card');
                card.classList.add('flex-row');
                
                const imgContainer = item.querySelector('.product-img-container');
                imgContainer.style.width = '200px';
                imgContainer.style.height = 'auto';
            });
        });
        
        document.getElementById('grid-view').addEventListener('click', function() {
            document.getElementById('list-view').classList.remove('active');
            this.classList.add('active');
            
            const container = document.getElementById('product-container');
            const items = document.querySelectorAll('.product-item');
            
            container.classList.remove('list-view');
            items.forEach(item => {
                item.classList.add('col-md-4', 'col-sm-6');
                item.classList.remove('col-12', 'mb-3');
                
                // Revert to grid view
                const card = item.querySelector('.product-card');
                card.classList.remove('flex-row');
                
                const imgContainer = item.querySelector('.product-img-container');
                imgContainer.style.width = '';
                imgContainer.style.height = '200px';
            });
        });
    </script>
</body>
</html>