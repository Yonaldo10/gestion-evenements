<?php
require_once 'check_admin.php';
require_once 'db_config.php';

// Récupération de l'événement à éditer
if (!isset($_GET['id'])) {
    header("Location: admin.php");
    exit;
}

$event_id = intval($_GET['id']);
$event = $conn->query("SELECT * FROM evenements WHERE id = $event_id")->fetch_assoc();

if (!$event) {
    $_SESSION['error'] = "Événement introuvable";
    header("Location: admin.php");
    exit;
}

// Traitement du formulaire de modification
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = trim($_POST['titre']);
    $description = trim($_POST['description']);
    $date_debut = $_POST['date_debut'];
    $date_fin = $_POST['date_fin'] ?? null;
    $lieu = trim($_POST['lieu']);
    $categorie = $_POST['categorie'];
    $places_max = $_POST['places_max'];
    $prix = $_POST['prix'];

    // Gestion de l'image
    $image_url = $event['image_url'];
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "uploads/";
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
            // Supprimer l'ancienne image si elle existe
            if (!empty($image_url) && file_exists($image_url)) {
                unlink($image_url);
            }
            $image_url = $target_file;
        }
    }

    // Mise à jour en BDD
    $stmt = $conn->prepare("UPDATE evenements SET 
                          titre = ?, description = ?, date_debut = ?, date_fin = ?, 
                          lieu = ?, image_url = ?, categorie = ?, places_max = ?, prix = ?
                          WHERE id = ?");
    $stmt->bind_param("sssssssidi", $titre, $description, $date_debut, $date_fin, 
                     $lieu, $image_url, $categorie, $places_max, $prix, $event_id);

    if ($stmt->execute()) {
        $_SESSION['success'] = "Événement mis à jour avec succès";
        header("Location: admin.php");
    } else {
        $_SESSION['error'] = "Erreur lors de la mise à jour: " . $conn->error;
        header("Location: editer_evenement.php?id=$event_id");
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Modifier l'événement</title>
    <link rel="stylesheet" href="styleadmin.css">
    <link rel="stylesheet" href="style_evenement.css">
</head>
<body>
    <?php include 'header.php'; ?>
    
    <div class="admin-container">
        <h2>Modifier l'événement</h2>

        <div class="admin-actions">
            <a href="admin.php">← Retour à la liste</a>
        </div>

        <?php if(isset($_SESSION['error'])): ?>
            <div class="alert alert-error"><?= $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        
        <form action="editer_evenement.php?id=<?= $event_id ?>" method="post" enctype="multipart/form-data">
            <div class="form-group">
                <label>Titre*</label>
                <input type="text" name="titre" value="<?= htmlspecialchars($event['titre']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Description*</label>
                <textarea name="description" rows="5" required><?= htmlspecialchars($event['description']) ?></textarea>
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Date et heure de début*</label>
                    <input type="datetime-local" name="date_debut" 
                           value="<?= date('Y-m-d\TH:i', strtotime($event['date_debut'])) ?>" required>
                </div>
                
                <div class="form-group">
                    <label>Date et heure de fin</label>
                    <input type="datetime-local" name="date_fin" 
                           value="<?= $event['date_fin'] ? date('Y-m-d\TH:i', strtotime($event['date_fin'])) : '' ?>">
                </div>
            </div>
            
            <div class="form-group">
                <label>Lieu*</label>
                <input type="text" name="lieu" value="<?= htmlspecialchars($event['lieu']) ?>" required>
            </div>
            
            <div class="form-group">
                <label>Image actuelle</label>
                <?php if(!empty($event['image_url'])): ?>
                    <img src="<?= $event['image_url'] ?>" width="100"><br>
                <?php endif; ?>
                <label>Nouvelle image</label>
                <input type="file" name="image" accept="image/*">
            </div>
            
            <div class="form-row">
                <div class="form-group">
                    <label>Catégorie</label>
                    <select name="categorie">
                        <option value="conference" <?= $event['categorie'] == 'conference' ? 'selected' : '' ?>>Conférence</option>
                        <option value="concert" <?= $event['categorie'] == 'concert' ? 'selected' : '' ?>>Concert</option>
                        <option value="atelier" <?= $event['categorie'] == 'atelier' ? 'selected' : '' ?>>Atelier</option>
                    </select>
                </div>
                
                <div class="form-group">
                    <label>Places disponibles</label>
                    <input type="number" name="places_max" min="1" value="<?= $event['places_max'] ?>">
                </div>
                
                <div class="form-group">
                    <label>Prix (€)</label>
                    <input type="number" name="prix" min="0" step="0.01" value="<?= $event['prix'] ?>">
                </div>
            </div>
            
            <button type="submit" class="btn">Mettre à jour</button>
        </form>
    </div>
    
    <?php include 'footer.php'; ?>
</body>
</html>