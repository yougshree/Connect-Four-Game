<?php
session_start();
include 'db_config.php';

header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$response = ['status' => 'error', 'message' => 'Unknown error'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    $board = isset($data['board']) ? json_encode($data['board']) : null;
    $current_player = isset($data['current_player']) ? $data['current_player'] : null;
    $player1_moves = isset($data['player1_moves']) ? $data['player1_moves'] : null;
    $player2_moves = isset($data['player2_moves']) ? $data['player2_moves'] : null;

    if ($board && $current_player && $player1_moves !== null && $player2_moves !== null) {
        $sql = "UPDATE game_state SET board='$board', current_player='$current_player', player1_moves=$player1_moves, player2_moves=$player2_moves ORDER BY id DESC LIMIT 1";
        if ($conn->query($sql) === TRUE) {
            $response = [
                'status' => 'success',
                'player1_moves' => $player1_moves,
                'player2_moves' => $player2_moves
            ];
        } else {
            $response['message'] = "Error: " . $conn->error;
        }
    } else {
        $response['message'] = 'Missing required parameters';
    }
} else {
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
?>