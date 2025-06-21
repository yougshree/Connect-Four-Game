<?php
include 'db_config.php';

header('Content-Type: application/json');

$result = $conn->query("SELECT COUNT(*) as count FROM players");
if ($result) {
    $row = $result->fetch_assoc();
    echo json_encode(['count' => $row['count']]);
} else {
    echo json_encode(['count' => 0, 'error' => $conn->error]);
}
?>