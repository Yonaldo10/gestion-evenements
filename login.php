<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion - Gestion d'Événements</title>
    <link rel="stylesheet" href="styles-compte.css">
</head>
<body>
    <header>
        <h1>Gestion d'Événements</h1>
        <nav>
            <a href="index2.php">Accueil</a>
            <a href="register.php">S'inscrire</a>
            <a href="login.php">Se connecter</a>
        </nav>
    </header>
    <div class="form-container">
    <?php if(isset($_SESSION['error'])): ?>
        <div class="alert alert-error">
        <span class="alert-icon">⚠️</span>
            <span><?= htmlspecialchars($_SESSION['error']) ?></span>
            <button class="close-alert" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['error']); ?>
    <?php endif; ?>
        <h2>Connexion</h2>
        <form action="process_login.php" method="post">
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="btn">Se connecter</button>
        </form>
        <p>Pas encore de compte ? <a href="register.php">Inscrivez-vous</a></p>
    </div>
</body>
</html>