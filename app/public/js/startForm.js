function createJoinForms() {
    let games = document.querySelectorAll('.game-list');
    let username = document.getElementById('username');

    games.forEach((game) => {
        let playerNames = game.querySelectorAll('.username');
        let usernameExists = Array.from(playerNames)
            .map(playerName => playerName.innerText)
            .includes(username.innerText);

        if (usernameExists) {
            return;
        }

        playerNames.forEach((playerName) => {
            if (playerName.innerText === '') {
                let form = document.createElement('form');
                form.method = 'post';
                form.action = '/join';

                let usernameInput = document.createElement('input');
                usernameInput.type = 'hidden';
                usernameInput.name = 'player';
                usernameInput.value = playerName.id;
                form.appendChild(usernameInput);

                let gameIdInput = document.createElement('input');
                gameIdInput.type = 'hidden';
                gameIdInput.name = 'room';
                gameIdInput.value = playerName.getAttribute('room');
                form.appendChild(gameIdInput);

                let button = document.createElement('button');
                button.type = 'submit';
                button.className = 'btn btn-outline-success';
                button.innerText = 'Join';
                form.appendChild(button);
                playerName.appendChild(form);
            }
        });
    });
}

function setupRadioButtons() {
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
}

createJoinForms();
setupRadioButtons();