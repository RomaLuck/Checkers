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
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        }
    });
}

function loadDefaultData() {
    fetch('/update', {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    })
        .then(response => {
            if (!response.ok) {
                throw new Error('Network response was not ok');
            }
            return response.json();
        })
        .then(data => {
            publishData(data)
        })
        .catch(error => {
            console.error('There was a problem with the fetch operation:', error);
        });
}

function handleUpdates() {
    document.addEventListener('DOMContentLoaded', function () {
        fetch('/discover')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Failed to discover hub URL');
                }
                return response;
            })
            .then(response => {
                const hubUrl = response.headers.get('Link').match(/<([^>]+)>;\s+rel=(?:mercure|"[^"]*mercure[^"]*")/)[1];
                const hub = new URL(hubUrl, window.origin);
                hub.searchParams.append('topic', '/chat');
                const eventSource = new EventSource(hub);
                eventSource.onmessage = event => {
                    try {
                        const data = JSON.parse(event.data);
                        if (data.table && data.log) {
                            publishData(data)
                        }
                    } catch (e) {
                        console.error('Error parsing event data:', e);
                    }
                };
                eventSource.onerror = error => {
                    console.error('EventSource failed:', error);
                    eventSource.close();
                };
            })
            .catch(error => {
                console.error('There was a problem with the fetch operation:', error);
            });
    });
}

function publishData(data) {
    const chessBoardContainer = document.getElementById('table-responsive');
    chessBoardContainer.innerHTML = createBoard(data.table);
    showLog(data.log)
    rotateTable();
}

function createBoard(deskData) {
    if (!deskData || deskData.length === 0) {
        return '';
    }

    const letters = ['a', 'b', 'c', 'd', 'e', 'f', 'g', 'h'];
    const numbers = [1, 2, 3, 4, 5, 6, 7, 8];

    let tableHTML = '<table class="chess-board" id="chess-board">';

    tableHTML += '<tr>';
    tableHTML += '<th>';
    for (let number of numbers) {
        tableHTML += `<th>${number}</th>`;
    }
    tableHTML += '</tr>';

    for (let i = 0; i < deskData.length; i++) {
        tableHTML += '<tr>';
        tableHTML += `<th>${letters[i]}</th>`;
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

    tableHTML += '<tr>';
    tableHTML += '<th>';
    for (let number of numbers) {
        tableHTML += `<th>${number}</th>`;
    }
    tableHTML += '</tr>';

    tableHTML += '</table>';

    return tableHTML;
}

function showLog(logs) {
    if (!logs || logs.length === 0) {
        return '';
    }

    const logsContainer = document.getElementById('game-log');

    logsContainer.innerHTML = '';
    for (let log of logs) {
        let logElement = document.createElement('li');

        let logTime = document.createElement('span');
        logTime.innerText = '[' + log.time + '] ';
        logElement.appendChild(logTime);

        let logMessage = document.createElement('span');
        logMessage.innerText = log.message;
        logElement.appendChild(logMessage);

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

loadDefaultData();
handleTableClick();
handleUpdates();
