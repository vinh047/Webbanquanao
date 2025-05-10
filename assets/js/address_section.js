document.addEventListener("DOMContentLoaded", () => {
    const provinceSelect = document.getElementById("province");
    const districtSelect = document.getElementById("district");
    const wardSelect = document.getElementById("ward");
    const form = document.getElementById("formAddAddress");

    let editingId = null;

    const modalElement = document.getElementById("modalAddAddress");
    modalElement.addEventListener("hidden.bs.modal", () => {
        document.body.classList.remove("modal-open");
        const backdrops = document.querySelectorAll(".modal-backdrop");
        backdrops.forEach(b => b.remove());
    });


    // Load tỉnh
    fetch("https://provinces.open-api.vn/api/p/")
        .then(res => res.json())
        .then(data => {
            data.forEach(p => {
                const option = document.createElement("option");
                option.value = p.name;
                option.textContent = p.name.replace(/^Tỉnh |^Thành phố /, "");
                provinceSelect.appendChild(option);
            });
        });

    // Chọn tỉnh → load quận
    provinceSelect.addEventListener("change", () => {
        const provinceName = provinceSelect.value;
        districtSelect.innerHTML = '<option selected disabled>Chọn Quận/Huyện</option>';
        wardSelect.innerHTML = '<option selected disabled>Chọn Phường/Xã</option>';

        fetch("https://provinces.open-api.vn/api/p/")
            .then(res => res.json())
            .then(provinces => {
                const found = provinces.find(p => p.name === provinceName);
                if (!found) return;

                fetch(`https://provinces.open-api.vn/api/p/${found.code}?depth=2`)
                    .then(res => res.json())
                    .then(data => {
                        data.districts.forEach(d => {
                            const option = document.createElement("option");
                            option.value = d.name;
                            option.textContent = d.name;
                            districtSelect.appendChild(option);
                        });
                    });
            });
    });

    // Chọn quận → load phường
    districtSelect.addEventListener("change", () => {
        const districtName = districtSelect.value;

        fetch("https://provinces.open-api.vn/api/d/")
            .then(res => res.json())
            .then(districts => {
                const found = districts.find(d => d.name === districtName);
                if (!found) return;

                fetch(`https://provinces.open-api.vn/api/d/${found.code}?depth=2`)
                    .then(res => res.json())
                    .then(data => {
                        wardSelect.innerHTML = '<option selected disabled>Chọn Phường/Xã</option>';
                        data.wards.forEach(w => {
                            const option = document.createElement("option");
                            option.value = w.name;
                            option.textContent = w.name;
                            wardSelect.appendChild(option);
                        });
                    });
            });
    });

    // Bấm nút sửa → đổ dữ liệu vào form
    document.querySelectorAll(".btn-edit-address").forEach(btn => {
        btn.addEventListener("click", async () => {
            const address = JSON.parse(btn.dataset.address);
            editingId = address.address_id;

            form.reset();
            form.address_detail.value = address.address_detail;
            form.is_default.checked = address.is_default == 1;
            provinceSelect.value = address.province;

            // Load lại quận rồi gán
            provinceSelect.dispatchEvent(new Event("change"));
            setTimeout(() => {
                districtSelect.value = address.district;
                districtSelect.dispatchEvent(new Event("change"));
                setTimeout(() => {
                    wardSelect.value = address.ward;
                }, 300);
            }, 300);

            new bootstrap.Modal(document.getElementById("modalAddAddress")).show();
        });
    });

    // Bấm xoá
    document.querySelectorAll(".btn-delete-address").forEach(btn => {
        btn.addEventListener("click", async () => {
            const addressId = btn.dataset.id;
            if (!confirm("Bạn có chắc chắn muốn xoá địa chỉ này?")) return;

            try {
                const res = await fetch("/ajax/delete_address.php", {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify({ address_id: addressId })
                });

                const result = await res.json();
                if (result.success) {
                    location.reload();
                } else {
                    alert(result.message || "Không thể xoá địa chỉ.");
                }
            } catch (err) {
                console.error(err);
                alert("Lỗi máy chủ khi xoá.");
            }
        });
    });

    // Gửi form
    form.addEventListener("submit", async e => {
        e.preventDefault();

        const address_detail = form.address_detail.value.trim();
        const province = provinceSelect.value;
        const district = districtSelect.value;
        const ward = wardSelect.value;
        const is_default = form.is_default.checked ? 1 : 0;

        if (!address_detail || !province || !district || !ward) {
            alert("Vui lòng điền đầy đủ thông tin.");
            return;
        }

        const payload = { address_detail, province, district, ward, is_default };
        if (editingId) payload.address_id = editingId;

        const url = editingId ? "/ajax/update_address.php" : "/ajax/add_address.php";

        try {
            const res = await fetch(url, {
                method: "POST",
                headers: { "Content-Type": "application/json" },
                body: JSON.stringify(payload)
            });
            const result = await res.json();
            if (result.success) {
                location.reload();
            } else {
                alert(result.message || "Thao tác thất bại.");
            }
        } catch (err) {
            console.error(err);
            alert("Lỗi máy chủ.");
        }
    });
});
