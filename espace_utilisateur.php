<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

require_once 'db_config.php';
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Espace Utilisateur</title>
    <link rel="stylesheet" href="style-espace_utilisateur.css">
</head>
<body>
    <header>
    <div class="header-content">
        <div class="header-center">
            <h2>Bienvenue, <?php echo htmlspecialchars($_SESSION['user_prenom']); ?></h2>
            <h3>Mes événements</h3>
        </div>
     <div class="header-right">
    <a href="parametres.php" class="settings-btn">Paramètres</a>
    <form action="logout.php" method="post">
        <button type="submit" class="logout-btn">Déconnexion</button>
    </form>
</div>
    </div>
    <nav>
    <a href="index2.php">Accueil</a>
    <a href="calendrier.php">Calendrier</a>
    <a href="espace_utilisateur.php">Espace Utilisateur</a>
</nav>
</header>
        <main class="container">
            <div class="events-list">
            <?php
            $stmt = $conn->prepare("SELECT e.id, e.titre, e.date_debut, e.lieu, e.image_url 
                                  FROM evenements e 
                                  JOIN inscriptions i ON e.id = i.event_id 
                                  WHERE i.user_id = ? 
                                  ORDER BY e.date_debut");
            $stmt->bind_param("i", $_SESSION['user_id']);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '<div class="event-item">';
                    if (!empty($row['image_url'])) {
                        echo '<img src="'.htmlspecialchars($row['image_url']).'" alt="'.htmlspecialchars($row['titre']).'" width="200">';
                    }
                    echo '<h4>'.htmlspecialchars($row['titre']).'</h4>';
                    echo '<p>Date: '.date('d/m/Y H:i', strtotime($row['date_debut'])).'</p>';
                    echo '<p>Lieu: '.htmlspecialchars($row['lieu']).'</p>';
                    echo '<form action="desinscrire.php" method="post">';
                    echo '<input type="hidden" name="event_id" value="'.$row['id'].'">';
                    echo '<button type="submit" class="unregister-btn">Se désinscrire</button>';
                    echo '</form>';
                    echo '</div>';
                }
            } else {
                echo '<p>Vous n\'êtes inscrit à aucun événement pour le moment.</p>';
            }
            ?>
        </div>
    </main>
</body>
</html>