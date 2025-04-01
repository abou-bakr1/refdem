<?php
session_start();
require_once '../config/connexion.php';
require_once '../includes/functions.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: ../pages/connexion.php');
    exit();
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $current_password = $_POST['current-password'];
    $new_password = $_POST['new-password'];
    $confirm_password = $_POST['confirm-password'];
    $pdo = Connexion::pdo();

    // Vérifier si les mots de passe correspondent
    if ($new_password !== $confirm_password) {
        $_SESSION['error'] = "Les mots de passe ne correspondent pas.";
        header('Location: ../pages/compte.php');
        exit();
    }

    // Vérifier si le mot de passe actuel est correct
    $stmt = $pdo->prepare("SELECT mdp FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();

    if (!password_verify($current_password, $user['mdp'])) {
        $_SESSION['error'] = "Le mot de passe actuel est incorrect.";
        header('Location: ../pages/compte.php');
        exit();
    }

    // Vérifier la complexité du nouveau mot de passe
    if (strlen($new_password) < 8) {
        $_SESSION['error'] = "Le mot de passe doit contenir au moins 8 caractères.";
        header('Location: ../pages/compte.php');
        exit();
    }

    // Hasher le nouveau mot de passe
    $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);

    // Mettre à jour le mot de passe
    try {
        $stmt = $pdo->prepare("UPDATE users SET mdp = ? WHERE id = ?");
        $stmt->execute([$hashed_password, $user_id]);

        $_SESSION['success'] = "Votre mot de passe a été mis à jour avec succès.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour du mot de passe.";
    }
}

header('Location: ../pages/compte.php');
exit();
