<!-- 📁 layout/partials/address_modal.php -->
<div class="modal fade" id="modalAddAddress" tabindex="-1" aria-labelledby="modalAddAddressLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header border-0">
        <h5 class="modal-title" id="modalAddAddressLabel">Thêm địa chỉ mới</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Đóng"></button>
      </div>
      <div class="modal-body">
        <form id="formAddAddress" novalidate>
          <div class="row g-3">
            <div class="col-md-6">
              <input type="text" name="address_detail" class="form-control rounded-pill" placeholder="Địa chỉ cụ thể" required>
              <div class="invalid-feedback">Vui lòng nhập địa chỉ.</div>
            </div>
            <div class="col-md-6">
              <select name="province" id="province" class="form-select rounded-pill" required>
                <option value="">Chọn Tỉnh/Thành</option>
              </select>
            </div>
            <div class="col-md-6">
              <select name="district" id="district" class="form-select rounded-pill" required>
                <option value="">Chọn Quận/Huyện</option>
              </select>
            </div>
            <div class="col-md-6">
              <select name="ward" id="ward" class="form-select rounded-pill" required>
                <option value="">Chọn Phường/Xã</option>
              </select>
            </div>
            <div class="col-12">
              <div class="form-check">
                <input type="checkbox" class="form-check-input" id="is_default" name="is_default">
                <label class="form-check-label" for="is_default">Đặt làm mặc định</label>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer border-0">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Hủy</button>
        <button type="submit" form="formAddAddress" class="btn btn-primary">Lưu</button>
      </div>
    </div>
  </div>
</div>
