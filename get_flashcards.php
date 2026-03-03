<?php
session_start();
if (!isset($_SESSION['user_id'])) exit();

// Connect to DB
$conn = new mysqli("localhost","root","","users_b");
if ($conn->connect_error) die("Connection failed");

// Get deck_id from query string
$deck_id = intval($_GET['deck_id']);

// Fetch flashcards
$stmt = $conn->prepare("SELECT id, question, answer FROM flashcards WHERE deck_id=?");
$stmt->bind_param("i", $deck_id);
$stmt->execute();
$result = $stmt->get_result();

$cards = [];
while($row = $result->fetch_assoc()) {
    $cards[] = $row;
}

echo json_encode($cards);
$conn->close();
?>