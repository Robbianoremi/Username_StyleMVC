<?php
session_start(); // Démarrage de la session
$title = 'Profil'; // Déclaration du titre de la page
require '../core/functions.php'; // Inclusion du fichier de fonctions
require '../config/db.php';
logedIn(); // Appel de la fonction de connexion
$user = $_SESSION['profil']; // Récupération des données de l'utilisateur
include 'partials/head.php'; // Inclusion du fichier d'en-tête
include 'partials/menu.php'; // Inclusion du fichier de navigation
?>
<div class="container">
    <?php displayMessage(); ?>
    <h1 class="mt-3">Bonjour <?= $user['name'] ?></h1>
    <pre>id user : <?= $user['idutilisateur'] ?></pre>
    <p>Mon email: <?= $user['email'] ?></p>
    <p>Inscrit depuis le: <?= $user['date'] ?></p>
    <a class="btn btn-danger" href="controllers/logout.php">déconnexion</a>
    <?php displayUsersAdmin(); ?>
</div>

<?php
include 'partials/footer.php'; // Inclusion du fichier de pied de page