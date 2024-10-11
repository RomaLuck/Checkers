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
    let radioButtons = document.querySelectorAll('.team-button');
    radioButtons.forEach((radioButton) => {
        radioButton.addEventListener('click', function () {
            let radioInput = radioButton.querySelector('input');
            radioInput.checked = true;
            radioButtons.forEach((button) => {
                button.classList.remove('selected');
            })
            radioButton.classList.add('selected');
        });
    })
}

function complexity() {
    let checkersComplexityBlock = document.getElementById('checkers-complexity');
    let checkersComputerRadio = document.getElementById('checkers-computer-strategy');

    checkersComputerRadio.addEventListener('click', function () {
        checkersComplexityBlock.classList.remove('d-none');
    })

    let chessComplexityBlock = document.getElementById('chess-complexity');
    let chessComputerRadio = document.getElementById('chess-computer-strategy');

    chessComputerRadio.addEventListener('click', function () {
        chessComplexityBlock.classList.remove('d-none');
    })
}

createJoinForms();
setupRadioButtons();
complexity();