<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản lý cửa hàng</title>
    <link rel="icon" type="./Images/png" href="/assets/img/logo_favicon/favicon.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.5/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-SgOJa3DmI69IUzQ2PVdRZhwQ+dy64/BUtbMJw1MZ8t5HZApcHrRKUc4W0kG879m7" crossorigin="anonymous">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.2/css/all.min.css" integrity="sha512-Evv84Mr4kqVGRNSgIGL/F/aIDqQb7xQ2vcrdIwxfjThSH8CSR7PBEakCr51Ck+w+/U6swU2Im1vVX0SVk9ABhg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../../assets/fonts/font.css">
    <link rel="stylesheet" href="./assets/css/sanpham.css">

    <?php
    require_once(__DIR__ . '/../../database/DBConnection.php');
    $db = DBConnect::getInstance();
    $color = $db->select("SELECT * FROM colors",[]);
    $size = $db->select("SELECT * FROM sizes ORDER BY size_id ASC",[]);
    ?>

</head>
<body>
    
            <div class="sanpham py-3" style="font-size: 19px;display:none;">


                <form action="../ajax/insertBienThe.php" method="POST" id="formNhapSPbienThe" enctype="multipart/form-data">
                    <div class="">
                        <label for="txtMa">Mã sản phẩm : </label>
                        <input type="text" name="txtMa" id="txtMa" placeholder="Mã của sản phẩm" class="form-control ">
                    </div>
    
                    <div class="pt-3 pb-2">
                        <label for="fileAnh">Hình ảnh : </label>
                        <input type="file" name="fileAnh" id="fileAnh" class="form-control">
                        <div class="pt-2" style="max-width:170px;max-height: 200px;" id="hienthianh">
                            <img src="" alt="" class="img-fluid" style="width: 170px; height: 200px; object-fit: contain; display: none;">
                        </div>
                    </div>
    
                    <div class="">
                        <label for="cbSize">Size : </label>
                        <select name="cbSize" id="cbSize" class="form-select ">
                            <option value="">Chọn size sản phẩm</option>
                            <?php foreach($size as $s): ?>
                            <option value="<?=$s['size_id']?>"><?=$s['name']?></option>
                            <?php endforeach ?>
                        </select>
                    </div>
    
                    <div class="pt-3">
                        <label for="txtSl">Số lượng sản phẩm : </label>
                        <input type="text" name="txtSl" id="txtSl" class="form-control" readonly placeholder="Số lượng của sản phẩm">
                    </div>

                    <div class="pt-3">
                        <label for="cbMau">Màu : </label>
                        <select name="cbMau" id="cbMau" class="form-select">
                            <option value="">Chọn màu sản phẩm</option>
                            <?php foreach($color as $cl): ?>

                                <option value="<?=$cl['color_id']?>"><?=$cl['name']?></option>


                            <?php endforeach ?>
                        </select>
                    </div>
    
                    <div class="pt-3">
                        <button class="btn btn-outline-primary" type="submit">Thêm biến thể</button>
                    </div>
                </form>

            </div>

            <div class="hienthi">
                <table class="table table-dark table-striped table-sm">
                    <thead>
                        <tr class="text-center">
                            <th class="hienthiidbt">ID BT</th>
                            <th class="hienthiidsp">ID SP</th>
                            <th class="hienthianh">Hình ảnh</th>
                            <th class="hienthisize">Size</th>
                            <th class="hienthigia">Số lượng</th>
                            <th class="hienthimau">Màu</th>
                            <th class="hienthibtn">Xử lý</th>

                        </tr>
                    </thead>
                    <tbody id="product-list">

                    </tbody>    
                    
                </table>
            </div>
            
            <div id="pagination"></div>


        </div>


        <div class="thongbaoLoi  bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
            </p>
        </div>

        <div class="thongbaoThanhCong  bg-success me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
            </p>
        </div>

        <div class="thongbaoUpdateThanhCong  bg-success me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Cập nhật thông tin thành công
            </p>
        </div>

        <div class="thongbaoXoaThanhCong  bg-success me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Xóa biến thể thành công
            </p>
        </div>

        <div class="thongbaoXoaHiddenThanhCong  bg-success me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Do tồn tại trong hóa đơn nên chỉ ẩn đi
            </p>
        </div>

        <div class="thongbaoXoaThatBai  bg-danger me-3 mt-3 p-3 rounded-2">
            <p class="mb-0 text-white">       
                Xóa biến thể thất bại
            </p>
        </div>

        <div class="overlay"></div>

        <div class="thongBaoXoa rounded-2">
            <p class="mb-0 fs-5 text-center">
                Bạn có chắc chắn muốn xóa hay không?       
            </p>
        
            <div class="d-flex justify-content-center gap-3 mt-2">
                <div class="">
                <button class="btn btn-danger" style="width:80px;">Có</button>
                </div>
                <div class="">
                <button class="btn btn-primary" style="width:80px;">Không</button>

                </div>
            </div>

        </div>       
        
            </div>
        <div class="formSua border container-md p-4">
            <div class="" style="font-size: 16px;">
                <p class="mb-0 text-center fs-4">Sửa thông tin sản phẩm</p>
                <form action="../ajax/updateBienthe.php" method="POST" id="formSuaSPbienThe" enctype="multipart/form-data">
                <input type="hidden" name="txtMaCTPN" id="txtMaCTPN">
                    <div class="">
                        <label for="txtMaBt">Mã biến thể : </label>
                        <input type="text" name="txtMaBt" id="txtMaBt" placeholder="Mã của biến thể" class="form-control" readonly>
                    </div>
                    <div class="pt-3">
                        <label for="txtMa">Mã sản phẩm : </label>
                        <input type="text" name="txtMaSua" id="txtMaSua" placeholder="Mã của sản phẩm" class="form-control ">
                    </div>
    
                    <div class="pt-3 pb-2">
                        <label for="fileAnh">Hình ảnh : </label>
                        <input type="file" name="fileAnhSua" id="fileAnhSua" class="form-control">
                        <div class="d-flex">
                        <div class="pt-2" style="max-width:170px;max-height: 200px;" id="hienthianhSua">
                            <img src="" alt="" class="img-fluid" style="width: 170px; height: 200px; object-fit: contain; display: none;">
                        </div>
                        <div id="tenFileAnhSua" class="text-muted small fst-italic mt-1 ms-2"></div>
                        </div>
                    </div>
    
                    <div class="d-flex">
                    <div class="me-auto">
                        <label for="cbSize">Size : </label>
                        <select name="cbSizeSua" id="cbSizeSua" class="form-select ">
                            <option value="">Chọn size sản phẩm</option>
                            <?php foreach($size as $s): ?>
                            <option value="<?=$s['size_id']?>"><?=$s['name']?></option>
                            <?php endforeach ?>

                        </select>
                    </div>

                    <div class="">
                        <label for="cbMau">Màu : </label>
                        <select name="cbMauSua" id="cbMauSua" class="form-select">
                            <option value="">Chọn màu sản phẩm</option>
                            <?php foreach($color as $cl): ?>

                                <option value="<?=$cl['color_id']?>"><?=$cl['name']?></option>


                            <?php endforeach ?>
                        </select>
                    </div>
                    </div>
    
                    <div class="pt-3">
                        <label for="txtSl">Số lượng sản phẩm : </label>
                        <input type="text" name="txtSlSua" id="txtSlSua" class="form-control" readonly placeholder="Số lượng của sản phẩm">
                    </div>

    
                    <div class="pt-3 d-flex justify-content-center gap-3">
                        <div class="">
                            <button class="btn btn-success" type="submit" style="width:80px;">Xác nhận</button>
                        </div>
                        <div class="">
                            <button class="btn btn-danger" type="button" style="width:80px;">Hủy</button>
                        </div>
                    </div>
                </form>

                
            </div>
            </div>
    </section>




      <script src="./assets/js/fetch_bienthe.js"></script>
</body>
</html>