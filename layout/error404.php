

<script src="https://cdnjs.cloudflare.com/ajax/libs/lottie-web/5.9.6/lottie.min.js"></script>

<style>
  .lottie-animation {
    width: 700px;
    height: 400px;
    margin: 0 auto; 
  }

</style>

<div class="error-container pt-2 pb-5">
    <div class="lottie-animation"></div>
    <div class="error-content text-center">
      <p class="fs-1">404</p>
      <p class="fs-3">Không tìm thấy sản phẩm.</p>
      <a href="index.php?page=sanpham" class="text-decoration-none btn btn-outline-primary">
        Quay về trang sản phẩm
      </a>
    </div>
</div>

<script>
  const animation = lottie.loadAnimation({
    container: document.querySelector('.lottie-animation'),
    renderer: 'svg',
    loop: true,
    autoplay: true,
    path: 'https://lottie.host/d987597c-7676-4424-8817-7fca6dc1a33e/BVrFXsaeui.json'
  });
</script>