<?php
session_start();
require_once 'db_config.php';

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Récupération des événements avec conversion des dates
$events_result = $conn->query("SELECT * FROM evenements");
$events_data = [];
while ($event = $events_result->fetch_assoc()) {
    // Conversion des dates pour JavaScript
    $event['js_date'] = date('Y-m-d', strtotime($event['date_debut']));
    $events_data[] = $event;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Calendrier</title>
    <link rel="stylesheet" href="style-calendrier.css">
</head>
<body>
    <header>
        <div class="header-wrapper">
            <h1>Calendrier</h1>
            <div class="user-controls">
                <span>Connecté en tant que: <?= htmlspecialchars($_SESSION['user_prenom']) ?></span>
                <form action="logout.php" method="post">
                    <button type="submit" class="logout-btn">Déconnexion</button>
                </form>
            </div>
            <nav>
                <a href="index2.php">Accueil</a>
                <a href="espace_utilisateur.php">Mon Espace</a>
                <?php if($_SESSION['is_admin'] ?? false): ?>
                    <a href="ajout_evenement.php">Ajouter Événement</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <div class="calendar-wrapper">
        <div class="calendar-container">
        <div class="month-navigation">
    <span id="prevMonth" class="arrow">&#9665;</span>
    <span id="currentMonth"></span>
    <span id="nextMonth" class="arrow">&#9655;</span>
</div>
            <div class="calendar" id="calendar"></div>
        </div>

        <div id="eventDetails" class="event-details-sidebar">
            <h3>Détails de l'événement</h3>
            <div id="eventDetailsContent">
                <p>Sélectionnez un jour avec événement</p>
            </div>
        </div>
    </div>

    <script>
    const events = <?= json_encode($events_data) ?>;
    console.log("Événements chargés:", events);
    </script>
    <script src="newcalendrier.js"></script>
</body>
</html>