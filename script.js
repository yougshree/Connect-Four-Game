document.addEventListener('DOMContentLoaded', function() {
    const boardElement = document.getElementById('board');
    const endGameButton = document.getElementById('endGameButton');
    const rows = 6;
    const cols = 7;
    const playerMovesElement = document.getElementById('playerMoves');
    let playerMoves = playerMovesElement ? parseInt(playerMovesElement.innerText) : 0;
    let currentPlayer = playerColor; 
    let board = []; 
    console.log(boardElement);

    function createBoard() {
        boardElement.innerHTML = '';
        for (let r = 0; r < rows; r++) {
            for (let c = 0; c < cols; c++) {
                const cell = document.createElement('div');
                cell.classList.add('cell');
                cell.dataset.row = r;
                cell.dataset.col = c;
                if (board[r][c] === 1) {
                    cell.classList.add('red');
                } else if (board[r][c] === 2) {
                    cell.classList.add('yellow');
                }
                cell.addEventListener('click', handleClick);
                boardElement.appendChild(cell);
            }
        }
    }

    function handleClick(event) {
        const row = parseInt(event.target.dataset.row);
        const col = parseInt(event.target.dataset.col);
        if (board[row][col] === 0 && currentPlayer === playerColor) {
            board[row][col] = playerColor === 'red' ? 1 : 2;
            playerMoves++;
            if (playerMovesElement) {
                playerMovesElement.innerText = playerMoves;
            }
            if (checkWinner(row, col)) {
                alert(`Congratulations ${playerName}! You played well.`);
                endGame();
            } else {
                currentPlayer = currentPlayer === 'red' ? 'yellow' : 'red'; 
                updateBoard();
            }
        }
    }



    function checkWinner(row, col) {
        const color = board[row][col];
        return checkDirection(row, col, 1, 0, color) || // Horizontal
               checkDirection(row, col, 0, 1, color) || // Vertical
               checkDirection(row, col, 1, 1, color) || // Diagonal /
               checkDirection(row, col, 1, -1, color) || // Diagonal \
               checkSquare(row, col, color); // Square
    }

    function checkDirection(row, col, rowDir, colDir, color) {
        let count = 0;
        for (let i = -3; i <= 3; i++) {
            const r = row + i * rowDir;
            const c = col + i * colDir;
            if (r >= 0 && r < rows && c >= 0 && c < cols && board[r][c] === color) {
                count++;
                if (count === 4) return true;
            } else {
                count = 0;
            }
        }
        return false;
    }

    function checkSquare(row, col, color) {
        const directions = [
            [0, 1], [1, 0], [1, 1], [1, -1]
        ];
        for (let [rowDir, colDir] of directions) {
            let count = 0;
            for (let i = 0; i < 4; i++) {
                const r = row + i * rowDir;
                const c = col + i * colDir;
                if (r >= 0 && r < rows && c >= 0 && c < cols && board[r][c] === color) {
                    count++;
                } else {
                    break;
                }
            }
            if (count === 4) return true;
        }
        return false;
    }

    function updateBoard() {
        const player1MovesElement = document.getElementById('player1Moves');
        const player2MovesElement = document.getElementById('player2Moves');

        const requestBody = {
            board: board,
            current_player: currentPlayer,
            player1_moves: playerColor === 'red' ? playerMoves : (player1MovesElement ? player1MovesElement.innerText : 0),
            player2_moves: playerColor === 'yellow' ? playerMoves : (player2MovesElement ? player2MovesElement.innerText : 0)
        };

        console.log('Request body:', requestBody); 

        fetch('update_board.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(requestBody)
        }).then(response => response.text()) 
        .then(data => {
            console.log('Raw response data:', data); 
            try {
                const jsonData = JSON.parse(data); 
                if (jsonData.status === 'success') {
                    createBoard();
                    if (player1MovesElement) {
                        player1MovesElement.innerText = jsonData.player1_moves;
                    }
                    if (player2MovesElement) {
                        player2MovesElement.innerText = jsonData.player2_moves;
                    }
                } else {
                    console.error('Error updating board:', jsonData.message);
                }
            } catch (error) {
                console.error('Error parsing JSON:', error);
                console.error('Response data:', data); 
            }
        }).catch(error => {
            console.error('Error:', error);
        });
    }

    function fetchBoard() {
        fetch('fetch_board.php')
            .then(response => response.json())
            .then(data => {
                board = data.board; // Use let for board
                currentPlayer = data.current_player;
                createBoard();
                const player1MovesElement = document.getElementById('player1Moves');
                const player2MovesElement = document.getElementById('player2Moves');
                if (player1MovesElement) {
                    player1MovesElement.innerText = data.player1_moves;
                }
                if (player2MovesElement) {
                    player2MovesElement.innerText = data.player2_moves;
                }
            }).catch(error => {
                console.error('Error fetching board:', error);
            });
    }

    function checkStatus() {
        fetch('check_status.php')
            .then(response => response.json())
            .then(data => {
                if (data.count < 2) {
                    document.getElementById('game').innerHTML = 'Waiting for another player to connect...';
                } else {
                    fetchBoard();
                    setInterval(fetchBoard, 1000); 
                }
            }).catch(error => {
                console.error('Error:', error);
            });
    }


    function endGame() {
        fetch('end_game.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            }
        }).then(response => response.json()).then(data => {
            if (data.status === 'success') {
                alert('Game ended successfully.');
                window.location.href = 'index.html';
            } else {
                console.error('Error ending game:', data.message);
            }
        }).catch(error => {
            console.error('Error:', error);
        });
    }

    endGameButton.addEventListener('click', endGame);

    checkStatus();
});