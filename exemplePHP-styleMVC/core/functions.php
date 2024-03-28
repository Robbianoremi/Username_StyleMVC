<?php
function displayUsers(){ // Fonction pour afficher les utilisateurs
    global $pdo; // Utilisez l'objet PDO que vous avez créé dans db.php
    $sql = "SELECT name,email FROM utilisateur ORDER BY idutilisateur DESC"; // Requête SQL pour obtenir tous les noms d'utilisateur
    $stmt = $pdo->prepare($sql); // Préparation de la requête
    $stmt->execute(); // Exécution de la requête
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC); // Récupération de tous les résultats dans un tableau associatif
    echo '<ul class="list-group">'; // Début du marquage HTML pour la liste
    foreach ($users as $user) { // Parcours du tableau des résultats
        echo '<li class="list-group-item">' . $user['name'] .'<br>'. $user['email'] . '<br>'. '</li>'; // Affichage de chaque nom d'utilisateur ainsi que son email
    }
    echo '</ul>'; // Fin du marquage HTML pour la liste
}

function displayUsersAdmin(){
  global $pdo;
  $sql = "SELECT idutilisateur, name, email FROM utilisateur ORDER BY idutilisateur DESC";
  $stmt = $pdo->prepare($sql);
  $stmt->execute();
  $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

  echo '<table class="table">'; // Début du tableau HTML
  echo '<thead>'; // En-tête du tableau
  echo '<tr>'; // Première ligne de l'en-tête
  echo '<th>Name</th>'; // Colonne pour le nom
  echo '<th>Email</th>'; // Colonne pour l'email
  echo '<th>Action</th>'; // Colonne pour l'action (suppression)
  echo '</tr>'; // Fin de la première ligne de l'en-tête
  echo '</thead>'; // Fin de l'en-tête du tableau
  echo '<tbody>'; // Corps du tableau

  foreach ($users as $user) {
      echo '<tr>'; // Nouvelle ligne pour chaque utilisateur
      echo '<td>' . $user['name'] . '</td>'; // Affichage du nom de l'utilisateur
      echo '<td>' . $user['email'] . '</td>'; // Affichage de l'email de l'utilisateur
      echo '<td><a href="controllers/delete_user.php?id=' . $user['idutilisateur'] . '" class="btn btn-danger" onclick="alert(\'Etes vous sur de supprimé ce compte\')" >Delete</a></td>'; // Lien de suppression avec passage de l'idclient dans l'URL
      echo '</tr>'; // Fin de la ligne
  }

  echo '</tbody>'; // Fin du corps du tableau
  echo '</table>'; // Fin du tableau HTML
}
function logedIn() { // Fonction pour vérifier si l'utilisateur est connecté et sa redirection suivant son rôle
  if (!isset($_SESSION['profil'])) { // Si l'utilisateur n'est pas connecté
      $_SESSION['flash']['danger'] = 'Vous devez être connecté pour accéder à cette page'; // Message d'erreur
      header('Location: ../index.php'); // Redirection vers la page de connexion
      exit; // Arrêt du script
  }
  $roles = $_SESSION['profil']['roles']; // Récupération des rôles de l'utilisateur
 
 
  $currentPage = basename($_SERVER['PHP_SELF']); // Récupération du nom de la page actuelle

  if ($currentPage === 'admin.php' && !in_array('ROLE_ADMIN', $roles)) { 
      // Si la page est admin.php et que l'utilisateur n'a pas le rôle ROLE_ADMIN
      $_SESSION['flash']['danger'] = 'Vous n\'avez pas les droits pour accéder à la page d\'administration';
      header('Location: index.php'); // Redirection vers la page de connexion
      exit; // Arrêt du script
  }
}
function checkLogedOut(){ // Fonction pour vérifier si l'utilisateur est déconnecté
  if (isset($_GET['logout']) && $_GET['logout'] == 'success') { // Vérification de la déconnexion
    echo "<div class='row'><div class='alert alert-success col-6 m-auto p-3 my-3'>Vous etes bien déconnecté</div></div>";
  }
  return null;
}
function displayMessage(){ // Fonction pour afficher les messages de la session flash
  if(isset($_SESSION['flash']['danger'])){ // Vérification de l'existence du message de la session
    echo '<div class="alert alert-danger">' . htmlspecialchars($_SESSION['flash']['danger']) . '</div>'; // Affichage du message de la session
    unset($_SESSION['flash']['danger']); // Suppression du message de la session
  }elseif(isset($_SESSION['flash']['success'])){
      echo '<div class="alert alert-success">' . htmlspecialchars($_SESSION['flash']['success']) . '</div>'; // Affichage du message de la session
  }
}
function getUserByEmail($email) { // Fonction pour obtenir un utilisateur par son email
  global $pdo; // Utilisez l'objet PDO que vous avez créé dans db.php

  try {
      $stmt = $pdo->prepare('SELECT * FROM utilisateur WHERE email = :email');
      $stmt->execute(['email' => $email]);
      return $stmt->fetch(PDO::FETCH_ASSOC);
  } catch (PDOException $e) {
      // Gérer l'erreur, par exemple enregistrer dans un fichier de logs et/ou afficher un message générique à l'utilisateur
      error_log($e->getMessage());
      return null;
  }
}

function createUser($name, $email, $password, $date) { // Fonction pour créer un utilisateur
  global $pdo; // Utilisez l'objet PDO que vous avez créé dans db.php
  $hashPass = password_hash($password, PASSWORD_DEFAULT);
  $sql = "INSERT INTO utilisateur (name, email, password, date) VALUES (:name, :email, :password, :date)";
  $stmt = $pdo->prepare($sql);
  $stmt->bindParam(':name', $name);
  $stmt->bindParam(':email', $email);
  $stmt->bindParam(':password', $hashPass);
  $stmt->bindParam(':date', $date);
  if ($stmt->execute()) {
    $iduser = $pdo->lastInsertId();
    $role = 2;
    $sql = "INSERT INTO utilisateur_has_role (utilisateur_idutilisateur, role_idrole) VALUES (:iduser, :idrole)";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':iduser',$iduser);
    $stmt->bindParam(':idrole', $role);
    $stmt->execute();
    $user = ['idutilisateur' => $iduser, 'roles' => 'ROLE_USER'];
    return $user;
  }
  return false;
}
function validateUserExists($email) { // Fonction pour vérifier si l'utilisateur existe
  global $pdo; // Utilisez l'objet PDO que vous avez créé dans db.php
  $sql = "SELECT COUNT(*) FROM utilisateur WHERE email = :email"; // Requête SQL pour compter le nombre d'utilisateurs avec le même email
  $stmt = $pdo->prepare($sql); // Préparation de la requête
  $stmt->bindParam(':email', $email); // Liaison de la variable $email à la requête
  $stmt->execute(); // Exécution de la requête
  if ($stmt->fetchColumn() > 0) { // Si le nombre d'utilisateurs avec le même email est supérieur à 0
    return $_SESSION['flash']['danger'] = "Cette email est déjà utilisé.";
  }
  return null;
}
function validateNotEmpty($field, $fieldName) { // Fonction pour valider si un champ n'est pas vide
  if (empty($field)) {
      return "Le champ $fieldName ne peut pas être vide.";
  }
  return null;
}
function validateUsername($username) { // Fonction pour valider le nom d'utilisateur
  if (!preg_match('/^[a-zA-Z]{4,30}$/', $username)) {
      return "Le nom d'utilisateur doit être composé de 4 à 30 lettres et sans chiffres ou caractères spéciaux.";
  }
  return null;
}
function validateEmail($email) { // Fonction pour valider l'email
  if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return "L'adresse email n'est pas valide.";
  }
  return null;
}
function validatePassword($password) { // Fonction pour valider le mot de passe
  // Vérifie si le mot de passe a une longueur de 4 à 30 caractères et contient au moins une lettre minuscule, une lettre majuscule et un chiffre
  if (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)[a-zA-Z\d]{4,30}$/', $password)) {
      return "Le mot de passe doit être composé de 4 à 30 caractères, incluant au moins une majuscule, une minuscule et un chiffre.";
  }
  return null;
}



