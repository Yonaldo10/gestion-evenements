<?php
require_once 'check_admin.php';
require_once 'db_config.php';
session_start();

// Vérification admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

if (!isset($_GET['event_id'])) {
    header("Location: admin_evenements.php");
    exit;
}

$event_id = intval($_GET['event_id']);

// Récupérer les infos de l'événement
$event_query = $conn->prepare("SELECT titre FROM evenements WHERE id = ?");
$event_query->bind_param("i", $event_id);
$event_query->execute();
$event_result = $event_query->get_result();
$event = $event_result->fetch_assoc();

if (!$event) {
    header("Location: admin_evenements.php");
    exit;
}

// Récupérer la liste des inscrits
$query = $conn->prepare("SELECT u.id, u.nom, u.prenom, u.email, i.date_inscription 
                         FROM utilisateurs u 
                         JOIN inscriptions i ON u.id = i.user_id 
                         WHERE i.event_id = ? 
                         ORDER BY i.date_inscription DESC");
$query->bind_param("i", $event_id);
$query->execute();
$result = $query->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Liste des inscrits</title>
    <link rel="stylesheet" href="styleadmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <h2>Liste des inscrits pour : <?= htmlspecialchars($event['titre']) ?></h2>
        
        <div class="admin-actions">
            <a href="admin_evenements.php" class="btn-back">
                <i class="fas fa-arrow-left"></i> Retour
            </a>
        </div>
        
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Date d'inscription</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['nom']) ?></td>
                    <td><?= htmlspecialchars($user['prenom']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($user['date_inscription'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>