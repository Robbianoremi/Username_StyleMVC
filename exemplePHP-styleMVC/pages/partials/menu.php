<nav class="navbar navbar-expand-lg bg-body-tertiary">
  <div class="container-fluid">
    <a class="navbar-brand" href="index.php">Espace Membre</a>
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="collapse navbar-collapse" id="navbarNav">
      <ul class="navbar-nav">
        <li class="nav-item">
          <a class="nav-link" href="index.php">Accueil</a>
        </li>
        <?php if (isset($_SESSION['profil'])) : ?> <!-- Vérification de la connexion de l'utilisateur pour afficher le menu -->
        <li class="nav-item"> 
          <a class="nav-link" href="profil">Profil</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="controllers/logout.php">Déconnexion</a>
        </li>
        <?php else : ?> <!--  Sinon afficher le menu de connexion et d'inscription -->
        <li class="nav-item">
          <a class="nav-link" href="register">S'inscrire</a>
        </li>
        <li class="nav-item">
          <a class="nav-link" href="login">Connexion</a>
        </li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>