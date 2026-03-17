<?php
require_once 'check_admin.php';
require_once 'db_config.php';
session_start();

// Vérification du statut admin
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    header("Location: login.php");
    exit;
}

// Configuration du délai pour considérer un utilisateur comme "connecté" (en secondes)
$temps_connection = 300; // 5 minutes

// Récupérer les utilisateurs actifs
$query = $conn->prepare("
    SELECT id, nom, prenom, email, last_activity 
    FROM utilisateurs 
    WHERE last_activity > DATE_SUB(NOW(), INTERVAL ? SECOND)
    ORDER BY last_activity DESC
");
$query->bind_param("i", $temps_connection);
$query->execute();
$result = $query->get_result();

// Compter le nombre d'utilisateurs connectés
$nombre_connectes = $result->num_rows;

// Récupérer le nombre total d'utilisateurs
$total_users = $conn->query("SELECT COUNT(*) as total FROM utilisateurs")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Statistiques des Utilisateurs</title>
    <link rel="stylesheet" href="styleadmin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <h2>Statistiques des Utilisateurs</h2>
        
        <div class="admin-actions">
            <a href="admin.php">Gestion des Événements</a>
            <a href="gestion_utilisateurs.php">Gestion des Utilisateurs</a>
            <a href="admin_evenements.php">Inscriptions aux Événements</a>
            <a href="logout.php">Déconnexion</a>
        </div>
        
        <div class="stats-container">
            <div class="stats-card">
                <h3><i class="fas fa-users"></i> Utilisateurs Connectés</h3>
                <div class="stat-value"><?= $nombre_connectes ?></div>
                <div class="stat-label">sur <?= $total_users ?> utilisateurs</div>
            </div>
            
            <div class="stats-card">
                <h3><i class="fas fa-chart-line"></i> Taux de Connexion</h3>
                <div class="stat-value"><?= round(($nombre_connectes/$total_users)*100, 1) ?>%</div>
                <div class="stat-label">d'utilisateurs actifs</div>
            </div>
        </div>
        
        <div class="chart-container">
            <canvas id="connectionChart"></canvas>
        </div>
        
        <h3>Détail des Utilisateurs Connectés</h3>
        <table class="admin-table">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Nom</th>
                    <th>Prénom</th>
                    <th>Email</th>
                    <th>Dernière Activité</th>
                </tr>
            </thead>
            <tbody>
                <?php while($user = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= $user['id'] ?></td>
                    <td><?= htmlspecialchars($user['nom']) ?></td>
                    <td><?= htmlspecialchars($user['prenom']) ?></td>
                    <td><?= htmlspecialchars($user['email']) ?></td>
                    <td><?= date('d/m/Y H:i', strtotime($user['last_activity'])) ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
    
    <script>
    // Graphique des connexions
    document.addEventListener('DOMContentLoaded', function() {
        const ctx = document.getElementById('connectionChart').getContext('2d');
        const chart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Connectés', 'Non connectés'],
                datasets: [{
                    data: [<?= $nombre_connectes ?>, <?= $total_users - $nombre_connectes ?>],
                    backgroundColor: [
                        'rgba(54, 162, 235, 0.7)',
                        'rgba(255, 99, 132, 0.7)'
                    ],
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'bottom',
                    },
                    title: {
                        display: true,
                        text: 'Statut des Utilisateurs'
                    }
                }
            }
        });
    });
    </script>
</body>
</html>