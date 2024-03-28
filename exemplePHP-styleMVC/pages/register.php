<?php
session_start(); // Démarrage de la session
$title = 'Inscription'; // Déclaration du titre de la page
require '../core/functions.php'; // Inclusion du fichier de fonctions
include 'partials/head.php'; // Inclusion du fichier d'en-tête
include 'partials/menu.php'; // Inclusion du fichier de menu
?>
<div class="row mt-5">
<h1 class="mt-3 text-center">Formulaire d'inscription</h1>

    <div class="col-6 m-auto border border-1 rounded p-3">
    <?php displayMessage(); ?>
        <form method="POST" action="controllers/addUser.php">
            <div class="mb-3">
                <label for="name" class="form-label">Username</label>
                <input type="text" name="name" id="name" class="form-control" >
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" name="email" id="email" class="form-control" >
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" name="password" id="password" class="form-control">
            </div>
            
            <input type="submit" name="submit" class="btn btn-primary">
        </form>
    </div>
</div>
    
<?php
include 'partials/footer.php';

