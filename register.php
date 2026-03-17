<?php
session_start();
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="styles-compte.css">
</head>
<body>
    <header>
        <h1>Gestion d'Événements</h1>
        <nav>
            <a href="index2.php">Accueil</a>
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
    
    <?php if(isset($_SESSION['success'])): ?>
        <div class="alert alert-success">
        <span class="alert-icon">✓</span>
            <span><?= htmlspecialchars($_SESSION['success']) ?></span>
            <button class="close-alert" onclick="this.parentElement.remove()">×</button>
        </div>
        <?php unset($_SESSION['success']); ?>
    <?php endif; ?>
        <h2>Inscription</h2>
        <form action="process_register.php" method="post">
            <div class="form-group">
                <label for="nom">Nom :</label>
                <input type="text" id="nom" name="nom" required>
            </div>
            <div class="form-group">
                <label for="prenom">Prénom :</label>
                <input type="text" id="prenom" name="prenom" required>
            </div>
            <div class="form-group">
                <label for="email">Email :</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="form-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirmer le mot de passe :</label>
                <input type="password" id="confirm_password" name="confirm_password" required>
            </div>
            <button type="submit" class="btn">S'inscrire</button>
        </form>
        <p>Déjà inscrit ? <a href="login.php">Connectez-vous</a></p>
    </div>
</body>
</html>