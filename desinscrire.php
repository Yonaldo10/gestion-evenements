<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['user_id']) || !isset($_POST['event_id'])) {
    header("Location: login.php");
    exit;
}

$event_id = intval($_POST['event_id']);
$user_id = $_SESSION['user_id'];

$stmt = $conn->prepare("DELETE FROM inscriptions WHERE user_id = ? AND event_id = ?");
$stmt->bind_param("ii", $user_id, $event_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Désinscription réussie";
} else {
    $_SESSION['error'] = "Erreur lors de la désinscription";
}

header("Location: espace_utilisateur.php");
exit;
?>