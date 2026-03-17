<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['event_id'])) {
    header("Location: index2.php");
    exit;
}

$event_id = intval($_GET['event_id']);
$user_id = $_SESSION['user_id'];

// Vérifier si l'utilisateur est déjà inscrit
$check = $conn->prepare("SELECT id FROM inscriptions WHERE user_id = ? AND event_id = ?");
$check->bind_param("ii", $user_id, $event_id);
$check->execute();
$check->store_result();

if ($check->num_rows > 0) {
    $_SESSION['error'] = "Vous êtes déjà inscrit à cet événement";
    header("Location: index2.php");
    exit;
}

// Vérifier les places disponibles
$event = $conn->query("SELECT places_max FROM evenements WHERE id = $event_id")->fetch_assoc();
$inscriptions = $conn->query("SELECT COUNT(*) as count FROM inscriptions WHERE event_id = $event_id")->fetch_assoc();

if ($event['places_max'] && $inscriptions['count'] >= $event['places_max']) {
    $_SESSION['error'] = "Désolé, plus de places disponibles pour cet événement";
    header("Location: index2.php");
    exit;
}

// Inscrire l'utilisateur
$stmt = $conn->prepare("INSERT INTO inscriptions (user_id, event_id) VALUES (?, ?)");
$stmt->bind_param("ii", $user_id, $event_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Inscription réussie!";
} else {
    $_SESSION['error'] = "Erreur lors de l'inscription";
}

header("Location: espace_utilisateur.php");
exit;
?>