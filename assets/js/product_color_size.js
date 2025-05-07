(function() {
    document.body.addEventListener("click", function (e) {
        if (e.target.classList.contains("color-thumb")) {
            const img = e.target;
            const productId = img.dataset.productId;
            const colorId = img.dataset.colorId;
            const newSrc = img.dataset.image;
            const container = img.closest('.border.rounded-1');
            const mainImg = container.querySelector("#main-image-" + productId);
            if (mainImg) mainImg.src = newSrc;

            container.querySelectorAll(".color-thumb").forEach(el => el.classList.remove("selected"));
            img.classList.add("selected");

            fetch('ajax/get_sizes_by_color.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify({ product_id: productId, color_id: colorId })
            })
            .then(res => res.text())
            .then(html => {
                const sizeGroup = container.querySelector('.size-group');
                sizeGroup.innerHTML = html;
                sizeGroup.classList.remove('d-none');
            })
            .catch(err => console.error("Lỗi gọi size:", err));
        }

        if (e.target.classList.contains('size-thumb')) {
            const container = e.target.closest('.border.rounded-1');
            container.querySelectorAll('.size-thumb').forEach(el => el.classList.remove('selected'));
            e.target.classList.add('selected');
        }
    });
})();
// Gán class 'selected' cho màu
document.querySelectorAll('.color-option').forEach(el => {
    el.addEventListener('click', function () {
        document.querySelectorAll('.color-option').forEach(c => c.classList.remove('selected'));
        this.classList.add('selected');
    });
});

// Gán class 'selected' cho size
document.querySelectorAll('.size-option').forEach(el => {
    el.addEventListener('click', function () {
        document.querySelectorAll('.size-option').forEach(s => s.classList.remove('selected'));
        this.classList.add('selected');
    });
});
