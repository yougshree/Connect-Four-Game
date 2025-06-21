<?php
session_start();
include 'db_config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $player_name = $_POST['player_name'];
    $color = $_POST['color'];

    $sql = "INSERT INTO players (name, color) VALUES ('$player_name', '$color')";
    if ($conn->query($sql) === TRUE) {
        $_SESSION['player_name'] = $player_name;
        $_SESSION['color'] = $color;

        // Check if both players are registered
        $result = $conn->query("SELECT COUNT(*) as count FROM players");
        $row = $result->fetch_assoc();
        if ($row['count'] == 2) {
            // Initialize game state
            $initial_board = json_encode(array_fill(0, 6, array_fill(0, 7, 0)));
            $conn->query("INSERT INTO game_state (board, current_player) VALUES ('$initial_board', 'red')");
        }

        header('Location: game.php');
        exit();
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>