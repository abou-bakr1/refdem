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
    $new_email = filter_input(INPUT_POST, 'new-email', FILTER_SANITIZE_EMAIL);
    $pdo = Connexion::pdo();

    // Vérifier si l'email est valide
    if (!filter_var($new_email, FILTER_VALIDATE_EMAIL)) {
        $_SESSION['error'] = "L'adresse email n'est pas valide.";
        header('Location: ../pages/compte.php');
        exit();
    }

    // Vérifier si l'email n'est pas déjà utilisé
    $stmt = $pdo->prepare("SELECT id FROM users WHERE mail = ? AND id != ?");
    $stmt->execute([$new_email, $user_id]);
    if ($stmt->rowCount() > 0) {
        $_SESSION['error'] = "Cette adresse email est déjà utilisée.";
        header('Location: ../pages/compte.php');
        exit();
    }

    // Mettre à jour l'email
    try {
        $stmt = $pdo->prepare("UPDATE users SET mail = ? WHERE id = ?");
        $stmt->execute([$new_email, $user_id]);

        $_SESSION['success'] = "Votre adresse email a été mise à jour avec succès.";
    } catch (PDOException $e) {
        $_SESSION['error'] = "Une erreur est survenue lors de la mise à jour de l'email.";
    }
}

header('Location: ../pages/compte.php');
exit();
