<?php
$trangthai = $_GET['trangthai'] ?? 'dangnhap';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Sagkuto - Login</title>
  <link rel="icon" href="../../assets/img/logo_favicon/favicon.png" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" />
  <link rel="stylesheet" href="css/Login_Form.css" />
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
<div class="container-fluid d-flex justify-content-center align-items-center py-3" style="min-height: calc(100vh - 80px);">
  <div class="col-11 col-sm-8 col-md-6 col-lg-4 bg-white rounded-4 p-4 retro-shadow login">
    <form action="userdb_func.php" method="POST" id="mainformmainform" novalidate></form>
  </div>
</div>

  <!-- Truyền trạng thái từ PHP sang JS -->
  <script>
    const trangthai = "<?php echo $trangthai; ?>";
  </script>

  <!-- Script -->

  <script src="FormStatus.js"></script>
  <script src="Validate/AfterSubmitCheck.js"></script>
  <script src="Validate/validateForm.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>