<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <title>Tìm kiếm sản phẩm</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
  <link rel="stylesheet" href="basic_search.css">
</head>
<body>

<a href="#" class="icon" id="openSearch">
  <i class="fa-solid fa-magnifying-glass"></i>
</a>

<div id="searchOverlay" class="search-overlay">
  <div class="search-box">
    <button class="close-btn" id="closeSearch">&times;</button>
    <input type="text" id="searchInput" placeholder="Bạn tìm kiếm gì hôm nay...">
    <div id="searchResultBox"></div>
  </div>
</div>

<script src="search_overlay.js"></script>
<script src="search_ui.js"></script>
<script src="search_logic.js"></script>




</body>
</html>