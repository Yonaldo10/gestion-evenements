<?php
require_once 'check_admin.php';
require_once 'db_config.php';

if (!isset($_GET['id']) ){
    header("Location: admin.php");
    exit;
}

$event_id = intval($_GET['id']);

// Suppression de l'événement
$stmt = $conn->prepare("DELETE FROM evenements WHERE id = ?");
$stmt->bind_param("i", $event_id);

if ($stmt->execute()) {
    $_SESSION['success'] = "Événement supprimé avec succès";
} else {
    $_SESSION['error'] = "Erreur lors de la suppression: " . $conn->error;
}

header("Location: admin.php");
exit;
?>