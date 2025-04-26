<?php
$trangthai = $_GET['trangthai'] ?? 'dangnhap';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sagkuto - Login</title>
  <link rel="icon" href="../assets/img/logo_favicon/favicon.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <link rel="stylesheet" href="Login_Form.css" />
  <link rel="stylesheet" href="../../assets/css/header.css" />
</head>

<body>
  <header class="header shadow-lg" >
      <div class="logo_header ms-5 ">
        <a href="../../index.php">
          <img src="../../assets/img/logo_favicon/logo.png" alt="SAGKUTO Logo">
        </a>
        </div>
  </header>
  <div class="container mt-3 d-flex align-items-center justify-content-center">
    <div class="row w-100 g-3 flex-column flex-md-row justify-content-center align-items-center gap-5">

      <!-- Login/Register Form -->
      <div class="col-md-5 bg-white rounded-4 p-4 retro-shadow login">
        <form action="userdb_func.php" method="POST" id="mainformmainform"></form>
      </div>

      <!-- Carousel -->
      <div class="col-md-6 border border-3 border-dark-subtle bg-light rounded-4 shadow-sm d-none d-md-block">
        <div id="animation_background" class="carousel slide" data-bs-ride="carousel">
          <div class="carousel-inner rounded-3">
            <div class="carousel-item active">
              <img src="img/bg1.png" class="d-block w-100 h-100" />
            </div>
            <div class="carousel-item">
              <img src="img/bg2.png" class="d-block w-100 h-100" />
            </div>
            <div class="carousel-item">
              <img src="img/bg3.jpg" class="d-block w-100 h-100" />
            </div>
          </div>
          <button class="carousel-control-prev" type="button" data-bs-target="#animation_background" data-bs-slide="prev">
            <span class="carousel-control-prev-icon"></span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#animation_background" data-bs-slide="next">
            <span class="carousel-control-next-icon"></span>
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Truyền trạng thái từ PHP sang JS -->
  <script>
    const trangthai = "<?php echo $trangthai; ?>";
  </script>

  <!-- Script -->
  <script src="FormStatus.js"></script>
  <script src="AfterSubmitCheck.js"></script>
  <script src="validateForm.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
