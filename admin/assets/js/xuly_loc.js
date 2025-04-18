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