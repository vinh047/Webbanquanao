<style>
    .card-box {
        height: 100%;
        border-radius: 15px;
        transition: 0.3s;
        cursor: pointer;
    }

    .card-box:hover {
        background-color: #f0f0f0;
        transform: scale(1.02);
    }

    .icon {
        font-size: 40px;
        color: #0d6efd;
    }
</style>
<div class="container py-5">
    <h2 class="mb-4 text-center">Quản lý Thuộc Tính</h2>
    <div class="row g-4">
        <!-- Màu sắc -->
        <div class="col-md-6">
            <a href="index.php?page=thuoctinh&subpage=mausac&pageadmin=1" class="text-decoration-none text-dark">
                <div class="card shadow card-box p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-palette icon me-3"></i>
                        <div>
                            <h5 class="mb-1">Màu sắc</h5>
                            <p class="mb-0 text-muted">Quản lý danh sách màu sản phẩm</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Phương thức thanh toán -->
        <div class="col-md-6">
            <a href="index.php?page=thuoctinh&subpage=phuongthucthanhtoan&pageadmin=1" class="text-decoration-none text-dark">
                <div class="card shadow card-box p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-credit-card icon me-3"></i>
                        <div>
                            <h5 class="mb-1">Phương thức thanh toán</h5>
                            <p class="mb-0 text-muted">Quản lý các hình thức thanh toán</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Thể loại -->
        <div class="col-md-6">
            <a href="the_loai.php" class="text-decoration-none text-dark">
                <div class="card shadow card-box p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-tags icon me-3"></i>
                        <div>
                            <h5 class="mb-1">Thể loại</h5>
                            <p class="mb-0 text-muted">Quản lý phân loại sản phẩm</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>

        <!-- Size -->
        <div class="col-md-6">
            <a href="size.php" class="text-decoration-none text-dark">
                <div class="card shadow card-box p-4">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-ruler-combined icon me-3"></i>
                        <div>
                            <h5 class="mb-1">Kích thước (Size)</h5>
                            <p class="mb-0 text-muted">Quản lý size sản phẩm</p>
                        </div>
                    </div>
                </div>
            </a>
        </div>
    </div>
</div>