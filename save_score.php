<?php
session_start();
if (!isset($_SESSION['user_id'])) exit();

$data = json_decode(file_get_contents("php://input"), true);
$user_id = $_SESSION['user_id'];
$deck_id = intval($data['deck_id']);
$score = intval($data['score']);
$total = intval($data['total']);

$conn = new mysqli("localhost","root","","user_b");
$stmt = $conn->prepare("INSERT INTO quiz_results(user_id, deck_id, score, total) VALUES (?, ?, ?, ?)");
$stmt->bind_param("iiii", $user_id, $deck_id, $score, $total);
$stmt->execute();
$stmt->close();
?>