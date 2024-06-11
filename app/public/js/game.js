function handleTableClick() {
    const tableContainer = document.getElementById('table-responsive');
    let form1 = '';
    let form2 = '';

    tableContainer.addEventListener("click", function (event) {
        if (form1 === '' && event.target.parentNode.id !== '') {
            form1 = event.target.parentNode.id;
            event.target.parentNode.style.backgroundColor = 'red';
        } else if (event.target.parentNode.id !== form1 && form2 === '') {
            form2 = event.target.id;

            const formData = new FormData();
            formData.append('formData', JSON.stringify({
                form1: form1,
                form2: form2
            }));

            fetch('/update', {
                method: 'POST',
                body: formData
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.text();
                })
                .then(data => {
                    form1 = '';
                    form2 = '';
                    updateTable();
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        }
    });
}

function updateTable() {
    fetch('/update', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    }).then(response => {
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }
        return response.json();
    })
        .then(data => {
            const chessBoardContainer = document.getElementById('table-responsive');
            chessBoardContainer.innerHTML = createChessBoard(data.table);
            showLog(data.log)
            rotateTable();
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
}

function createChessBoard(deskData) {
    const letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
    const numbers = [1, 2, 3, 4, 5, 6, 7, 8];

    let tableHTML = '<table class="chess-board" id="chess-board">';

    tableHTML += '<tr>';
    for (let number of numbers) {
        tableHTML += `<th>${number}</th>`;
    }
    tableHTML += '</tr>';

    for (let i = 0; i < deskData.length; i++) {
        tableHTML += '<tr>';
        for (let j = 0; j < deskData[i].length; j++) {
            let pieceClass = '';
            switch (deskData[i][j]) {
                case 1:
                    pieceClass = 'white-checker';
                    break;
                case 2:
                    pieceClass = 'black-checker';
                    break;
                case 3:
                    pieceClass = 'white-king';
                    break;
                case 4:
                    pieceClass = 'black-king';
                    break;
                case -1:
                    pieceClass = 'clean-cell';
            }
            tableHTML += `<td id="${letters[i]}${j + 1}"><div class="${pieceClass}"></div></td>`;
        }
        tableHTML += `<th>${letters[i]}</th></tr>`;
    }

    tableHTML += '</table>';

    return tableHTML;
}

function showLog(logs) {
    const logsContainer = document.getElementById('game-log');

    logsContainer.innerHTML = '';
    for (let log of logs) {
        let logElement = document.createElement('li');
        logElement.classList.add('text-' + log.logLevel.toLowerCase());
        logElement.innerText = log.message;
        logsContainer.appendChild(logElement);
    }
}

function rotateTable() {
    const table = document.getElementById('table-responsive');
    let letters = document.querySelectorAll("table th");
    let color = document.getElementById('color');

    if (color.innerText.trim() === 'white') {
        table.style.transform = 'rotate(-90deg)';
        letters.forEach((letter) => {
            letter.style.transform = 'rotate(90deg)';
        });
    }
    if (color.innerText.trim() === 'black') {
        table.style.transform = 'rotate(90deg)';
        letters.forEach((letter) => {
            letter.style.transform = 'rotate(-90deg)';
        });
    }
}

updateTable();
handleTableClick();
setInterval(updateTable, 2000);
