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

        if (item.classList.contains('color-option')) {
            const colorId = item.getAttribute('data-color-id');
            const checkbox = document.querySelector(`input.color-checkbox[value="${colorId}"]`);
            if (checkbox) checkbox.checked = item.classList.contains('selected');
        }

        if (item.classList.contains('size-option')) {
            const sizeId = item.getAttribute('data-size-id');
            const checkbox = document.querySelector(`input.size-checkbox[value="${sizeId}"]`);
            if (checkbox) checkbox.checked = item.classList.contains('selected');
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


