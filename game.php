<?php
session_start();
include 'db_config.php';

if (!isset($_SESSION['player_name']) || !isset($_SESSION['color'])) {
    header('Location: index.html');
    exit();
}

$player_name = $_SESSION['player_name'];
$color = $_SESSION['color'];

// Fetch player names
$player1_name = '';
$player2_name = '';
$result = $conn->query("SELECT name, color FROM players");
while ($row = $result->fetch_assoc()) {
    if ($row['color'] == 'red') {
        $player1_name = $row['name'];
    } else {
        $player2_name = $row['name'];
    }
}

// Check if both players are registered
$result = $conn->query("SELECT COUNT(*) as count FROM players");
$row = $result->fetch_assoc();
if ($row['count'] < 2) {
    echo "Waiting for another player to connect...";
    exit();
}

$sql = "SELECT * FROM game_state ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $game_state = $result->fetch_assoc();
    $board = json_decode($game_state['board']);
    $current_player = $game_state['current_player'];
} else {
    // Initialize the board if no game state is found
    $board = array_fill(0, 6, array_fill(0, 7, 0));
    $current_player = 'red';
    $conn->query("INSERT INTO game_state (board, current_player, player1_moves, player2_moves) VALUES ('" . json_encode($board) . "', '$current_player', 0, 0)");
    $game_state = [
        'player1_moves' => 0,
        'player2_moves' => 0
    ];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connect Four</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <div id="game">
        <h2>Connect Four</h2>
        <div id="players">
            <div id="player1Info"><?php echo $player1_name; ?> (Red): <span id="player1Moves"><?php echo $game_state['player1_moves']; ?></span> moves</div>
            <div id="player2Info"><?php echo $player2_name; ?> (Yellow): <span id="player2Moves"><?php echo $game_state['player2_moves']; ?></span> moves</div>
        </div>
        <div id="board"></div>
        <button id="endGameButton">End Game</button>
    </div>
    <script>
        const board = <?php echo json_encode($board); ?>;
        let currentPlayer = '<?php echo $current_player; ?>';
        const playerColor = '<?php echo $color; ?>';
        const playerName = '<?php echo $player_name; ?>';
    </script>
    <script src="script.js"></script>
</body>
</html>