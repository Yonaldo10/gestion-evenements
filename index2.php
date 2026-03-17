<?php
session_start();
require_once 'db_config.php';

// Récupérer les événements à venir
$query = "SELECT * FROM evenements WHERE date_debut >= NOW() ORDER BY date_debut ASC";
$result = $conn->query($query);
$events = $result->fetch_all(MYSQLI_ASSOC);
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Gestion d'Événements</title>
    <link rel="stylesheet" href="style16.css">
</head>
<body>
    <header>
        <div class="header-wrapper">
            <h1>Gestion d'Événements</h1>
            <div class="search-container">
                <input type="text" id="searchBar" placeholder="Rechercher un événement..." onkeyup="searchEvent()">
                <button class="search-button">Rechercher</button>
            </div>
            <nav>
                <a href="index2.php">Accueil</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="calendrier.php">Calendrier</a>
                    <a href="espace_utilisateur.php">Espace Utilisateur</a>
                    <a href="contact.php">Contact</a>
                    <a href="logout.php">Déconnexion</a>
                <?php else: ?>
                    <a href="register.php">S'inscrire</a>
                    <a href="login.php">Connexion</a>
                <?php endif; ?>
                <?php if(isset($_SESSION['user_id']) && $_SESSION['is_admin']): ?>
                    <a href="admin.php">Admin</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    
        
    <h2>Événements à venir</h2>
<div class="events-container">
    <?php if(count($events) > 0): ?>
        <?php foreach($events as $event): ?>
            <div class="event-card">
    <?php if(!empty($event['image_url'])): ?>
        <img src="<?= htmlspecialchars($event['image_url']) ?>" alt="<?= htmlspecialchars($event['titre']) ?>">
    <?php else: ?>
        <img src="images/default-event.jpg" alt="Image par défaut">
    <?php endif; ?>
    
    <div class="event-card-content">
        <h3><?= htmlspecialchars($event['titre']) ?></h3>
        <p><strong>Date :</strong> <?= date('d/m/Y', strtotime($event['date_debut'])) ?></p>
        <p><strong>Heure :</strong> <?= date('H:i', strtotime($event['date_debut'])) ?></p>
        <p><strong>Lieu :</strong> <?= htmlspecialchars($event['lieu']) ?></p>
        <p><strong>Description :</strong> <?= htmlspecialchars($event['description']) ?></p>
        
        <?php if(isset($_SESSION['user_id'])): ?>
            <a href="inscription_evenement.php?event_id=<?= $event['id'] ?>" class="btn-inscription">S'inscrire</a>
        <?php endif; ?>
    </div>
</div>
        <?php endforeach; ?>
    <?php else: ?>
        <p class="no-events">Aucun événement à venir pour le moment.</p>
    <?php endif; ?>
</div>
    </main>
    <button class="back-to-top" title="Remonter en haut">↑</button>
    <script src="index.js"></script>
</body>
</html>