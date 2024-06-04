let whiteRadio = document.getElementById('whiteRadio');
let blackRadio = document.getElementById('blackRadio');
let whiteButton = document.getElementById('whiteButton');
let blackButton = document.getElementById('blackButton');

whiteButton.addEventListener('click', function () {
    whiteRadio.checked = true;
    whiteButton.classList.add('selected');
    blackButton.classList.remove('selected');
});

blackButton.addEventListener('click', function () {
    blackRadio.checked = true;
    blackButton.classList.add('selected');
    whiteButton.classList.remove('selected');
});