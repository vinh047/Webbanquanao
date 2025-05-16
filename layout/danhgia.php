<?php
// danhgia.php
session_start();
require_once __DIR__ . '/../database/DBConnection.php';

if (empty($_SESSION['user_id'])) {
    header('Location: /User-form/Login_Form/Login_Form.php');
    exit;
}

$db = DBConnect::getInstance();
$pdo = $db->getConnection();

$user_id = $_SESSION['user_id'];
$order_id = isset($_GET['order_id']) ? intval($_GET['order_id']) : 0;

// Lấy danh sách sản phẩm từ đơn hàng này
$stmt = $pdo->prepare("SELECT od.product_id, p.name
                       FROM order_details od
                       JOIN products p ON od.product_id = p.product_id
                       WHERE od.order_id = ?");
$stmt->execute([$order_id]);
$products = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Nếu form được submit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($_POST['rating'] as $product_id => $rating) {
        $comment = trim($_POST['comment'][$product_id]);

        // Kiểm tra đã đánh giá chưa
        $exists = $pdo->prepare("SELECT COUNT(*) FROM reviews WHERE user_id = ? AND product_id = ?");
        $exists->execute([$user_id, $product_id]);
        if ($exists->fetchColumn() > 0) continue; // Bỏ qua nếu đã đánh giá

        // Thêm đánh giá mới
        $insert = $pdo->prepare("INSERT INTO reviews (user_id, product_id, rating, comment) VALUES (?, ?, ?, ?)");
        $insert->execute([$user_id, $product_id, $rating, $comment]);
    }
    header("Location: /index.php?page=donhang");
    exit;
}
?>

<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Đánh giá sản phẩm</title>
  <link rel="stylesheet" href="/assets/bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="/assets/fonts/font.css">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" />
  <style>
    body {
      background-color: #f8f9fa;
    }
    .card {
      border-radius: 12px;
    }
    .card-header {
      background-color: #fff;
      font-size: 1.1rem;
    }
    textarea::placeholder {
      color: #aaa;
    }
  </style>
</head>
<body class="py-5">
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-lg-8">
        <div class="text-center mb-4">
          <h3 class="fw-bold text-dark">
            📝 Đánh giá sản phẩm trong đơn hàng <span class="text-primary">#<?= $order_id ?></span>
          </h3>
        </div>

        <?php if (!empty($products)): ?>
          <?php
            // Tính trung bình sao cho từng sản phẩm trong đơn hàng
            foreach ($products as &$product) {
              $stmt = $pdo->prepare("SELECT ROUND(AVG(rating), 1) AS avg_rating, COUNT(*) AS total_reviews FROM reviews WHERE product_id = ?");
              $stmt->execute([$product['product_id']]);
              $reviewData = $stmt->fetch(PDO::FETCH_ASSOC);
              $product['avg_rating'] = $reviewData['avg_rating'] ?? 0;
              $product['total_reviews'] = (int)($reviewData['total_reviews'] ?? 0);
            }
          ?>
        <?php endif; ?>

        <?php if (empty($products)): ?>
          <div class="alert alert-warning">Không tìm thấy sản phẩm nào trong đơn hàng này.</div>
        <?php else: ?>
          <form method="POST" class="vstack gap-4">
            <?php foreach ($products as $p): ?>
              <div class="card shadow-sm border-0">
                <div class="card-header">
                  <i class="fa-solid fa-box-open me-1 text-secondary"></i> <?= htmlspecialchars($p['name']) ?>
                  <div class="mt-2 small text-muted">
                    <span class="fw-bold text-warning" style="font-size: 1.2rem;">
                      <?= $p['avg_rating'] ?>
                    </span>
                    <span class="text-muted">trên 5</span>
                    <br/>
                    <?php for ($i = 1; $i <= 5; $i++): ?>
                      <?php if ($i <= round($p['avg_rating'])): ?>
                        <i class="fa-solid fa-star text-warning"></i>
                      <?php else: ?>
                        <i class="fa-regular fa-star text-warning"></i>
                      <?php endif; ?>
                    <?php endfor; ?>
                    <div class="text-muted mt-1">(<?= $p['total_reviews'] ?> đánh giá)</div>
                  </div>
                </div>
                <div class="card-body bg-light">
                  <div class="mb-3">
                    <label class="form-label fw-semibold">Đánh giá sao</label>
                    <select name="rating[<?= $p['product_id'] ?>]" class="form-select w-50" required>
                      <option value="">-- Chọn sao --</option>
                      <?php for ($i = 1; $i <= 5; $i++): ?>
                        <option value="<?= $i ?>"><?= str_repeat('⭐', $i) ?> (<?= $i ?> sao)</option>
                      <?php endfor; ?>
                    </select>
                  </div>

                  <div class="mb-2">
                    <label class="form-label fw-semibold">Bình luận của bạn</label>
                    <textarea name="comment[<?= $p['product_id'] ?>]" class="form-control" rows="3" placeholder="Hãy chia sẻ trải nghiệm của bạn..."></textarea>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>

            <div class="d-flex justify-content-between mt-4">
              <a href="/index.php?page=donhang" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left"></i> Quay về đơn hàng
              </a>
              <button type="submit" class="btn btn-primary px-4">
                <i class="fa-solid fa-paper-plane me-1"></i> Gửi đánh giá
              </button>
            </div>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
  <script src="/assets/bootstrap/js/bootstrap.bundle.min.js" defer></script>
</body>
</html>