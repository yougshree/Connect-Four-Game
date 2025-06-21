<?php
session_start();
include 'db_config.php';

header('Content-Type: application/json');

$response = ['status' => 'error', 'message' => 'Unknown error'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $sql = "DELETE FROM players";
    if ($conn->query($sql) === TRUE) {
        $sql = "DELETE FROM game_state";
        if ($conn->query($sql) === TRUE) {
            $response = ['status' => 'success'];
        } else {
            $response['message'] = "Error: " . $conn->error;
        }
    } else {
        $response['message'] = "Error: " . $conn->error;
    }
} else {
    $response['message'] = 'Invalid request method';
}

echo json_encode($response);
?>