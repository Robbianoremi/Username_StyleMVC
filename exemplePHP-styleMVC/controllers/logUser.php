<?php
session_start(); // Démarrage de la session
require_once '../config/db.php'; // Inclusion de la connexion à la base de données
require_once '../core/functions.php'; // Inclusion du fichier de fonctions

// Vérifier si le formulaire a été soumis
if (isset($_POST['submit'])) {
    $email = $_POST['email']; // Récupération de l'email
    $password = $_POST['password']; // Récupération du mot de passe
    // Validation des champs
    $emailError = validateNotEmpty($email, 'email') ?: validateEmail($email); // L'opérateur ?: est un opérateur ternaire simplifié qui fonctionne ainsi : si la première expression (avant le ?) est vraie (dans ce cas, si $email n'est pas vide), alors cette expression est retournée (et affectée à $emailError). Si elle est fausse (si $email est vide), alors la deuxième expression (après le :), validateEmail($email), est évaluée et son résultat est affecté à $emailError. 
    $passwordError = validateNotEmpty($password, 'password') ?: validatePassword($password); // Validation du mot de passe
    if ($emailError || $passwordError) {
        // Un des champs est incorrect, on stocke l'erreur et on redirige
        $_SESSION['flash']['danger'] = $emailError ?: $passwordError; // Enregistrement du message d'erreur dans la session
        header('Location: ../login'); // Rediriger vers la page de connexion
        exit; // Arrêt du script
    }
    // Récupérer l'utilisateur depuis la base de données
    $user = getUserByEmail($email); // Récupération de l'utilisateur par son email
    $sql = "SELECT `level` FROM utilisateur_has_role JOIN `role` ON idrole = role_idrole WHERE utilisateur_idutilisateur = :idutilisateur";
    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':idutilisateur', $user['idutilisateur']);
    $stmt->execute();
    $roleResult = $stmt->fetchAll(PDO::FETCH_COLUMN);

    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['profil'] = ['name' => $user['name'], 'idutilisateur' => $user['idutilisateur'], 'date' => $user['date'], 'roles' => $roleResult, 'email' => $user['email']];
        if (in_array("ROLE_ADMIN", $roleResult)) {
             // Stocker les infos utilisateur dans la session
            $_SESSION['flash']['success'] = "Vous êtes maintenant connecté."; // Message de connexion
            header('Location: ../admin'); // Rediriger vers une page de profil
        } elseif (in_array("ROLE_USER", $roleResult)) {
            
            $_SESSION['flash']['success'] = "Vous êtes maintenant connecté."; // Message de connexion
            header('Location: ../profil');
        } else {
            $_SESSION['flash']['danger'] = "Vous devez être connecté pour accéder a cette page"; // Message de connexion
        }
        // Rediriger vers une page de profil
        exit; // Arrêt du script
    } else {
        // Les identifiants sont incorrects, on prépare un message flash
        $_SESSION['flash']['danger'] = "Email ou mot de passe incorrect."; // Message d'erreur
        header('Location: ../login'); // Rediriger vers la page de connexion
        exit; // Arrêt du script
    }
}
