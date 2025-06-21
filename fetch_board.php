<?php
session_start();
include 'db_config.php';

header('Content-Type: application/json');

$sql = "SELECT * FROM game_state ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
if ($result->num_rows > 0) {
    $game_state = $result->fetch_assoc();
    $response = [
        'board' => json_decode($game_state['board']),
        'current_player' => $game_state['current_player'],
        'player1_moves' => $game_state['player1_moves'],
        'player2_moves' => $game_state['player2_moves']
    ];
    echo json_encode($response);
} else {
    echo json_encode(['error' => 'No game state found']);
}
?>