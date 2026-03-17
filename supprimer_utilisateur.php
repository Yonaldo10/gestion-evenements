<?php
require_once 'check_admin.php';
require_once 'db_config.php';
session_start();

// Vérification du statut admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Vérifier si l'ID est présent et valide
if (!isset($_GET['id']) {
    $_SESSION['error'] = "ID utilisateur manquant";
    header("Location: gestion_utilisateurs.php");
    exit;
}

$user_id = intval($_GET['id']);

// Empêcher l'auto-suppression
if ($user_id == $_SESSION['user_id']) {
    $_SESSION['error'] = "Vous ne pouvez pas supprimer votre propre compte";
    header("Location: gestion_utilisateurs.php");
    exit;
}

try {
    // Commencer une transaction
    $conn->begin_transaction();

    // 1. Supprimer les inscriptions aux événements
    $stmt = $conn->prepare("DELETE FROM inscriptions WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // 2. Supprimer l'utilisateur
    $stmt = $conn->prepare("DELETE FROM utilisateurs WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();

    // Valider la transaction
    $conn->commit();

    $_SESSION['success'] = "Utilisateur supprimé avec succès";
} catch (Exception $e) {
    // Annuler en cas d'erreur
    $conn->rollback();
    $_SESSION['error'] = "Erreur lors de la suppression : " . $e->getMessage();
}

header("Location: gestion_utilisateurs.php");
exit;
?>