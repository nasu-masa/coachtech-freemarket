document.getElementById('priceInput').addEventListener('input', function (e) {
    value = e.target.value.replace(/[^0-9]/g, '');

    e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ',');
});