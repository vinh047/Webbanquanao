console.log("✅ File xulyFilter.js đã load!");

document.getElementById('filter-icon').addEventListener('click', function () {
  const filterBox = document.querySelector('.filter_loc');
  filterBox.classList.toggle('show');
});

// document.addEventListener('click', function (e) {
//     const filterBox = document.querySelector('.filter_loc');
//     const icon = document.getElementById('filter-icon');

//     if (!filterBox.contains(e.target) && !icon.contains(e.target)) {
//         filterBox.classList.remove('show');
//     }
// });

document.addEventListener('click', function (e) {
  const filterBox = document.querySelector('.filter_loc');
  const icon = document.getElementById('filter-icon');

  // Nếu click không nằm trong filterBox, icon, hoặc dropdown của Select2 thì mới tắt
  const isInSelect2 = e.target.closest('.select2-container') !== null;

  if (!filterBox.contains(e.target) && !icon.contains(e.target) && !isInSelect2) {
    filterBox.classList.remove('show');
  }
});

// const selectItem = document.querySelectorAll('.selectable');
// selectItem.forEach(item =>{
//     item.addEventListener('click', () =>
//     {
//         item.classList.toggle('selected');
//     })
// })

const sortIcon = document.getElementById('sort-icon');
const sortMenu = document.getElementById('sort-menu');

sortIcon.addEventListener('click', () => {
  sortMenu.classList.toggle('show');
});

document.addEventListener('click', function (e) {
  if (!sortMenu.contains(e.target) && !sortIcon.contains(e.target)) {
    sortMenu.classList.remove('show');
  }
});



const selectedColors = [];
const selectedSizes = [];
// ✅ Xử lý chọn nhiều màu
document.querySelectorAll('.color-option').forEach((item, index) => {
  item.addEventListener('click', function () {
    const checkbox = document.querySelectorAll('.color-checkbox')[index];
    if (checkbox) {
      checkbox.checked = !checkbox.checked;

      if (checkbox.checked) {
        item.classList.add('border-3', 'border-dark', 'selected');
      } else {
        item.classList.remove('border-3', 'border-dark', 'selected');
      }
    }
  });
});

// ✅ Xử lý chọn nhiều size
document.querySelectorAll('.size-option').forEach((item, index) => {
  item.addEventListener('click', function () {
    const checkbox = document.querySelectorAll('.size-checkbox')[index];
    if (checkbox) {
      checkbox.checked = !checkbox.checked;

      if (checkbox.checked) {
        item.classList.add('border-3', 'border-dark', 'selected');
      } else {
        item.classList.remove('border-3', 'border-dark', 'selected');
      }
    }
  });
});


function updateHiddenInput(name, value, add) {
  const form = document.querySelector('form');
  if (add) {
    const input = document.createElement('input');
    input.type = 'hidden';
    input.name = name;
    input.value = value;
    input.setAttribute('data-dynamic', `${name}-${value}`);
    form.appendChild(input);
  } else {
    const input = form.querySelector(`input[data-dynamic="${name}-${value}"]`);
    if (input) input.remove();
  }
}

const resetButton = document.querySelector('button[type="reset"]');
resetButton.addEventListener('click', function () {
  document.querySelectorAll('.selectable').forEach(item => {
    item.classList.remove('selected');
  });

  document.querySelectorAll('input.color-checkbox, input.size-checkbox').forEach(input => {
    input.checked = false;
  });
});
document.body.addEventListener("click", function (e) {
  // 🟦 Click vào ảnh màu
  if (e.target.classList.contains("color-thumb")) {
    const img = e.target;
    const productId = img.dataset.productId;
    const colorId = img.dataset.colorId;
    const newSrc = img.dataset.image;
    const container = img.closest('.border.rounded-1');
    const mainImg = container.querySelector("#main-image-" + productId);

    // Đổi ảnh chính
    if (mainImg) mainImg.src = newSrc;

    // Bỏ chọn ảnh màu khác
    container.querySelectorAll(".color-thumb").forEach(el => el.classList.remove("selected"));
    img.classList.add("selected");

    // Reset size
    const sizeGroup = container.querySelector('.size-group');
    sizeGroup.innerHTML = '';
    container.querySelectorAll(".size-thumb").forEach(el => el.classList.remove("selected"));
    disableAddToCart(container);

    // Gọi AJAX để load size theo màu
    fetch('ajax/get_sizes_by_color.php', {
      method: 'POST',
      headers: { 'Content-Type': 'application/json' },
      body: JSON.stringify({ product_id: productId, color_id: colorId })
    })
      .then(res => res.text())
      .then(html => {
        sizeGroup.innerHTML = html;
        sizeGroup.classList.remove('d-none');
      });
  }

  // 🟦 Click vào size
  if (e.target.classList.contains("size-thumb")) {
    const sizeDiv = e.target;
    const container = sizeDiv.closest('.border.rounded-1');

    container.querySelectorAll(".size-thumb").forEach(el => el.classList.remove("selected"));
    sizeDiv.classList.add("selected");

    // Kiểm tra nếu đã chọn màu
    const hasColor = container.querySelector(".color-thumb.selected");
    if (hasColor) {
      enableAddToCart(container);
    }
  }

  // 🟦 Click vào nút thêm vào giỏ
  if (e.target.closest(".add-to-cart-btn")) {
    const btn = e.target.closest(".add-to-cart-btn");
    if (btn.disabled) return;

    const container = btn.closest('.border.rounded-1');
    const color = container.querySelector('.color-thumb.selected');

    const productId = color.dataset.productId;
    const productName = color.dataset.productName;
    const productPrice = color.dataset.price;
    const colorId = color.dataset.colorId;
    const colorName = color.dataset.colorName;
    const image = color.dataset.image.split('/').pop();

    const variantId = color.dataset.variantId;

    const size = container.querySelector('.size-thumb.selected');

    if (!color || !size) return;
    const sizeId = size.dataset.sizeId;
    const sizeName = size.dataset.sizeName;

    addToCart(productId, productName, productPrice, colorId, colorName, image, variantId, sizeId, sizeName);
  }
});

// 🟦 Hỗ trợ: Bật / tắt nút thêm giỏ
function enableAddToCart(container) {
  const btn = container.querySelector('.add-to-cart-btn');
  if (btn) btn.disabled = false;
}
function disableAddToCart(container) {
  const btn = container.querySelector('.add-to-cart-btn');
  if (btn) btn.disabled = true;
}
document.addEventListener('click', function (e) {
  // Nếu click KHÔNG nằm trong 1 sản phẩm
  const isInsideProduct = e.target.closest('.border.rounded-1');
  if (!isInsideProduct) {
    document.querySelectorAll('.border.rounded-1').forEach(container => {
      // 1. Bỏ chọn màu
      container.querySelectorAll('.color-thumb.selected').forEach(el => el.classList.remove('selected'));

      // 2. Bỏ chọn size
      container.querySelectorAll('.size-thumb.selected').forEach(el => el.classList.remove('selected'));

      // 3. Ẩn bảng size
      const sizeGroup = container.querySelector('.size-group');
      if (sizeGroup) {
        sizeGroup.classList.add('d-none');
        sizeGroup.innerHTML = '';
      }

      // 4. Disable nút giỏ hàng
      disableAddToCart(container);
    });
  }
});

const btnTatBoLoc = document.getElementById('btn-tatboloc');
if (btnTatBoLoc) {
  btnTatBoLoc.addEventListener('click', function (e) {
    e.stopPropagation(); // ⛔ Ngăn sự kiện lan ra ngoài
    const filterBox = document.querySelector('.filter_loc');
    if (filterBox) {
      filterBox.classList.remove('show'); // ✅ Ẩn bộ lọc
    }
  });
}


