<?php
    require_once '../database/DBConnection.php';

    $db = DBConnect::getInstance();

    $product_id = isset($_GET['product_id']) ? (int)$_GET['product_id'] : 0;

    $limit = 2;
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $page = max($page, 1);
    
    $rating = isset($_GET['rating']) && $_GET['rating'] !== 'all' ? (int) $_GET['rating'] : 'all';

    if($rating !== 'all') {
        $total_reviews = $db->selectOne("SELECT COUNT(*) AS total FROM reviews WHERE product_id = ? AND rating = ?", [$product_id, $rating]);
    }
    else {
        $total_reviews = $db->selectOne('SELECT COUNT(*) AS total FROM reviews WHERE product_id = ?', [$product_id]);
    }

    $total_pages = ceil($total_reviews['total'] / $limit);
    if($page > $total_pages && $total_pages != 0) {
        $page = $total_pages;
    }
    $offset = ($page - 1) * $limit;


    if($rating !== 'all') {
        $reviews = $db->select("SELECT r.*, u.name FROM reviews r
                                JOIN users u ON u.user_id = r.user_id
                                WHERE product_id = ? AND r.rating = ?
                                ORDER BY r.created_at DESC 
                                LIMIT $limit OFFSET $offset", [$product_id, $rating]);
    }
    else {
        $reviews = $db->select("SELECT r.*, u.name FROM reviews r
                                JOIN users u ON u.user_id = r.user_id
                                WHERE product_id = ? 
                                ORDER BY r.created_at DESC 
                                LIMIT $limit OFFSET $offset", [$product_id]);
    }

    

    ob_start();
    foreach($reviews as $r): ?>
        <div class="mt-3 px-4 border-bottom">
                                
            <div class="fw-semibold fs-6"><?= $r['name'] ?></div>
            <div class="ms-1 mt-1" style="font-size: 12px;">
                <?php for($i = 1; $i <= 5; $i++) {
                    if($i <= $r['rating'])
                        echo '<i class="fa-solid fa-star" style="color: #FFD43B;"></i>';
                    else 
                        echo '<i class="fa-regular fa-star" style="color: #FFD43B;"></i>';
                } ?>
            </div>
            <p class="mt-2" style="white-space: pre-line; font-size: 15px;"><?= $r['comment'] ?></p>
        </div>
    <?php endforeach;
    $reviewsHtml = ob_get_clean();

    ob_start();
    if($total_pages > 1): ?>
        <div class="pagination d-flex justify-content-center m-2 pe-5 gap-4 align-items-center">
            <button class="btn-prev">
                <i class="fa-solid fa-chevron-left"></i>     
            </button>
                            
            <div class="border p-2">
                <input type="text" class="pag" value="<?= $page ?>" size="1">
                <span class="user-select-none text-secondary">/</span>
                <span class="max-pag mx-2 user-select-none text-secondary"><?= $total_pages ?></span>

            </div>

            <button class="btn-next">
                <i class="fa-solid fa-angle-right"></i>
            </button>
        </div>
    <?php endif;

    $paginationHtml = ob_get_clean();

    echo $reviewsHtml . 'SPLIT' . $paginationHtml;
?>