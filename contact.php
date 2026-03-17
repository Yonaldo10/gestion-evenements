<?php
// Configuration de la base de données
$host = 'localhost';
$dbname = 'messagesdb';
$username = 'contactuser';
$password = 'ContactPass123!';

$messageEnvoye = "";
$erreur = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        $pdo = new PDO("mysql:host=$host;dbname=gestion_evenements;charset=utf8", "root", "root");
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $name = htmlspecialchars(trim($_POST["nom"]));
        $email = htmlspecialchars(trim($_POST["email"]));
        $message = htmlspecialchars(trim($_POST["message"]));

        if (!empty($name) && !empty($email) && !empty($message)) {
            $stmt = $pdo->prepare("INSERT INTO messages (name, email, message) VALUES (:name, :email, :message)");
            $stmt->execute([
                ':name' => $name,
                ':email' => $email,
                ':message' => $message
            ]);
            $messageEnvoye = "✅ Message envoyé avec succès.";
        } else {
            $erreur = "❌ Tous les champs sont requis.";
        }

    } catch (PDOException $e) {
        $erreur = "❌ Erreur de connexion à la base de données : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
  <meta charset="UTF-8">
  <title>Contactez-nous</title>
  <link rel="stylesheet" href="style14.css">
</head>
<body>
    <header>
        <div class="header-wrapper">
            <h1>Gestion d'Événements</h1>
            </div>
            <nav>
                <a href="index2.php">Accueil</a>
                <?php if(isset($_SESSION['user_id'])): ?>
                    <a href="calendrier.php">Calendrier</a>
                    <a href="espace_utilisateur.php">Espace Utilisateur</a>
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

  <div class="contact-form">
    <h2>Contactez-nous</h2>

    <?php if ($messageEnvoye): ?>
      <div class="message success"><?php echo $messageEnvoye; ?></div>
    <?php elseif ($erreur): ?>
      <div class="message error"><?php echo $erreur; ?></div>
    <?php endif; ?>

    <form action="contact.php" method="post">
      <div class="form-group">
        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" required>
      </div>

      <div class="form-group">
        <label for="email">Email :</label>
        <input type="email" id="email" name="email" required>
      </div>

      <div class="form-group">
        <label for="message">Message :</label>
        <textarea id="message" name="message" required></textarea>
      </div>

      <button type="submit">Envoyer</button>
    </form>
  </div>

</body>
</html>
