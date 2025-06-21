# Connect-Four-Game
<b>Frontend:</b>
index (HTML): User interface for the game
styles.css: Handles the game's visual styling
script.js: Manages client-side logic (e.g., user clicks, board updates)

<b>Backend:</b>
PHP (e.g., game.php, register.php, check_status.php): Handles server-side game logic, board state, player registration, and checking win conditions
SQL (this.sql): Likely contains table creation and data structure for storing game state
db_config.php: Stores database connection details

<b>How Multiplayer Gameplay Works</b>
The game is hosted locally using XAMPP and made accessible online through Ngrok, which generates a temporary public link (e.g., https://abc123.ngrok.io).
When one player shares the Ngrok link, another player can join the game from any device with internet access.Gameplay takes place in real-time. Each player takes turns dropping their tokens into the Connect Four board.The system uses AJAX to send and receive data between the frontend and the PHP backend, ensuring smooth updates without refreshing the page. The MySQL database stores the current game state, player turns, and board data, so both players always see the same synchronized board as they play.
