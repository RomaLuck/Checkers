const tableContainer = document.getElementById('table-responsive');
const tablePiece = document.querySelectorAll("table td");
const form1 = document.getElementById("form1");
const form2 = document.getElementById("form2");
const xhr = new XMLHttpRequest();
let whiteTeam;
let blackTeam;
let result;

tableContainer.addEventListener("click", function (event) {
    const target = event.target.classList.contains("white") ||
        event.target.classList.contains("black") ||
        event.target.classList.contains("white-piece") ||
        event.target.classList.contains("black-piece");

    if (target) {
        if (form1.value === "") {
            form1.value = event.target.id || event.target.parentNode.id;
        } else if (event.target.id !== form1.value || event.target.parentNode.id !== form1.value) {
            form2.value = event.target.id || event.target.parentNode.id;

            const formData = new FormData();
            formData.append('formData', JSON.stringify({
                form1: form1.value,
                form2: form2.value
            }));

            fetch('game.php', {
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
                    console.log(data);
                    form1.value = "";
                    form2.value = "";
                    updateTable();
                })
                .catch(error => {
                    console.error('There was a problem with the fetch operation:', error);
                });
        }
    }
});
async function updateTable() {
    try {
        const response = await fetch('request.php');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const result = await response.json();
        whiteTeam = result.jsonWhiteTeam;
        blackTeam = result.jsonBlackTeam;

        for (let i = 0; i < tablePiece.length; i++) {
            while (tablePiece[i].firstChild) {
                tablePiece[i].firstChild.remove();
            }
        }

        for (let i = 0; i < tablePiece.length; i++) {
            if (whiteTeam.includes(tablePiece[i].id)) {
                const piece = document.createElement('div');
                piece.className = "white-piece";
                tablePiece[i].appendChild(piece);
            }
        }

        for (let i = 0; i < tablePiece.length; i++) {
            if (blackTeam.includes(tablePiece[i].id)) {
                const piece = document.createElement('div');
                piece.className = "black-piece";
                tablePiece[i].appendChild(piece);
            }
        }
    } catch (error) {
        console.error('There was a problem with the fetch operation:', error);
    }
}
(async function () {
    try {
        const response = await fetch('request.php');
        if (!response.ok) {
            throw new Error('Network response was not ok');
        }

        const result = await response.json();
        whiteTeam = result.jsonWhiteTeam;
        blackTeam = result.jsonBlackTeam;

        for (let i = 0; i < tablePiece.length; i++) {
            if (whiteTeam.includes(tablePiece[i].id)) {
                const piece = document.createElement('div');
                piece.className = "white-piece";
                tablePiece[i].appendChild(piece);
            }
        }

        for (let i = 0; i < tablePiece.length; i++) {
            if (blackTeam.includes(tablePiece[i].id)) {
                const piece = document.createElement('div');
                piece.className = "black-piece";
                tablePiece[i].appendChild(piece);
            }
        }
    } catch (error) {
        console.error('There was a problem with the fetch operation:', error);
    }
})();
