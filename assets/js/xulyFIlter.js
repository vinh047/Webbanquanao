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


const selectItem = document.querySelectorAll('.selectable');
selectItem.forEach(item =>{
    item.addEventListener('click', () =>
    {
        item.classList.toggle('selected');
    })
})

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