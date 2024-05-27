function assignPiecesToCells() {
    const tablePieces = document.querySelectorAll("table td");

    for (let i = 0; i < tablePieces.length; i++) {
        const piece = document.createElement('div');
        const cellValue = parseInt(tablePieces[i].innerText, 10);
        if (cellValue === 1) {
            piece.className = "white-piece";
        } else if (cellValue === 2) {
            piece.className = "black-piece";
        }
        tablePieces[i].innerText = '';
        tablePieces[i].appendChild(piece);
    }
}

function handleTableClick() {
    const tableContainer = document.getElementById('table-responsive');
    const form1 = document.getElementById("form1");
    const form2 = document.getElementById("form2");

    tableContainer.addEventListener("click", function (event) {
        if (form1.value === "") {
            form1.value = event.target.id || event.target.parentNode.id;
        } else if (event.target.id !== form1.value || event.target.parentNode.id !== form1.value) {
            form2.value = event.target.id || event.target.parentNode.id;

            const formData = new FormData();
            formData.append('formData', JSON.stringify({
                form1: form1.value,
                form2: form2.value
            }));

            fetch('/game', {
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
                    location.reload();
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        }
    });
}

function rotateTable() {
    const table = document.getElementById('chess-board');
    const queueParam = document.getElementById('queue');

    if (queueParam.innerText.trim() === '1') {
        table.style.transform = 'rotate(-90deg)';
    }
    if (queueParam.innerText.trim() === '-1') {
        table.style.transform = 'rotate(90deg)';
    }
}

assignPiecesToCells();
handleTableClick();
rotateTable();