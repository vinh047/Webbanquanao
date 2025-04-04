console.log("✅ File xulyFilter.js đã load!");

document.getElementById('filter-icon').addEventListener('click', function () {
    const filterBox = document.querySelector('.filter_loc');
    filterBox.classList.toggle('show');
});

document.addEventListener('click', function (e) {
    const filterBox = document.querySelector('.filter_loc');
    const icon = document.getElementById('filter-icon');

    if (!filterBox.contains(e.target) && !icon.contains(e.target)) {
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

document.querySelectorAll('.selectable').forEach(item => {
    item.addEventListener('click', () => {
        item.classList.toggle('selected');

        // Nếu là màu (dựa vào style.backgroundColor hoặc data-color-id)
        if (item.classList.contains('color-option')) {
            const colorId = item.getAttribute('data-color-id');
            updateHiddenInput('colors[]', colorId, item.classList.contains('selected'));
        }

        // Nếu là size
        if (item.classList.contains('size-option')) {
            const sizeVal = item.innerText.trim();
            updateHiddenInput('sizes[]', sizeVal, item.classList.contains('selected'));
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
    // Bỏ chọn UI
    document.querySelectorAll('.selectable').forEach(item => {
        item.classList.remove('selected');
    });

    // Xóa input hidden đã thêm
    document.querySelectorAll('input[data-dynamic]').forEach(input => {
        input.remove();
    });
});

