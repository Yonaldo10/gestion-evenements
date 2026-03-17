<?php require_once 'check_admin.php'; ?>
<?php
session_start();
require_once 'db_config.php';

// Vérification admin
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

if (!$_SESSION['is_admin']) {
    header("Location: index2.php");
    exit;
}

// Traitement du formulaire
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'] ?? null;
    $lieu = trim($_POST['lieu']);
    $categorie = $_POST['categorie'];
    $places_max = $_POST['places_max'];
    $prix = $_POST['prix'];

    // Upload image
if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
    $target_dir = "uploads/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0755, true);
    }
    // Générer un nom de fichier unique
    $file_extension = pathinfo($_FILES["image"]["name"], PATHINFO_EXTENSION);
    $target_file = $target_dir . uniqid() . '.' . $file_extension;
    
    if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
        $image_url = $target_file;
    } else {
        error_log("Échec de l'upload: " . $_FILES["image"]["error"]);
    }
}

    // Insertion en BDD
    $stmt = $conn->prepare("INSERT INTO evenements (titre, description, date_debut, date_fin, lieu, image_url, categorie, places_max, prix) 
                          VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssssid", $titre, $description, $date_debut, $date_fin, $lieu, $image_url, $categorie, $places_max, $prix);
    
    if ($stmt->execute()) {
        $_SESSION['success'] = "Événement ajouté avec succès!";
        header("Location: admin.php");
    } else {
        $_SESSION['error'] = "Erreur lors de l'ajout de l'événement";
        header("Location: ajout_evenement.php");
    }
    exit;

    if ($stmt->execute()) {
    $last_id = $conn->insert_id;
    error_log("Événement ajouté avec ID: $last_id");
    $_SESSION['success'] = "Événement ajouté avec succès! (ID: $last_id)";
    header("Location: admin.php");
} else {
    error_log("Erreur SQL: " . $conn->error);
    $_SESSION['error'] = "Erreur: " . $conn->error;
    header("Location: ajout_evenement.php");
}
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Événement</title>
    <link rel="stylesheet" href="styleadmin.css">
    <link rel="stylesheet" href="style_evenement.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <h2>Ajouter un Événement</h2>
        <div class="admin-actions">
            <a href="admin.php">← Retour au tableau</a>
        </div>
        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <form action="ajout_evenement.php" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Titre*</label>
                <input type="text" name="titre" required>
            </div>
            
            <div class="form-group">
                <label>Description*</label>
                <textarea name="description" rows="5" required></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Date et heure de début*</label>
                    <input type="datetime-local" name="date_debut" required>
                </div>
                
                <div class="form-group">
                    <label>Date et heure de fin</label>
                    <input type="datetime-local" name="date_fin">
                </div>
            </div>
            
            <div class="form-group">
                <label>Lieu*</label>
                <input type="text" name="lieu" required>
            </div>
            
            <div class="form-group">
                <label>Image</label>
                <input type="file" name="image" accept="image/*">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Catégorie</label>
                    <select name="categorie">
                        <option value="conference">Conférence</option>
                        <option value="concert">Concert</option>
                        <option value="atelier">Atelier</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Places disponibles</label>
                    <input type="number" name="places_max" min="1">
                </div>
                
                <div class="form-group">
                    <label>Prix (€)</label>
                    <input type="number" name="prix" min="0" step="0.01">
                </div>
            </div>
            
            <button type="submit" class="btn">Ajouter l'événement</button>
        </form>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>