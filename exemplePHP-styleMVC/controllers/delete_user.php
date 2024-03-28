<?php
session_start();
require '../config/db.php';
$userId = $_GET['id'];
$sql = "DELETE FROM utilisateur WHERE idutilisateur = :iduser";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':iduser', $userId);
$stmt->execute();
$_SESSION['flash']['success'] = "Vous avez bien effacer le compte utilisateur";
header('Location: ../admin');


?>