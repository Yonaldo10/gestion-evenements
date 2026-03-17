<?php
$servername = "localhost"; // À remplacer par votre serveur
$username = "root"; // À remplacer par vos identifiants
$password = ""; // vide sur XAMPP par défaut
$dbname = "gestion_evenements";

// Créer une connexion
$conn = new mysqli($servername, $username, $password, $dbname);

// Vérifier la connexion
if ($conn->connect_error) {
    die("Échec de la connexion : " . $conn->connect_error);
}
?>