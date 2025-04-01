<?php
session_start();
require_once '../config/connexion.php';

if (!isset($_GET['token']) || !isset($_GET['action'])) {
    header('Location: ../pages/forum.php');
    exit;
}

$token = $_GET['token'];
$action = $_GET['action'];

try {
    $pdo = Connexion::pdo();
    
    // Vérifier si l'invitation existe et est en attente
    $stmt = $pdo->prepare("SELECT * FROM group_invitations WHERE token = ? AND status = 'pending'");
    $stmt->execute([$token]);
    $invitation = $stmt->fetch();
    
    if (!$invitation) {
        $_SESSION['message'] = "Cette invitation n'est plus valide ou a déjà été utilisée.";
        header('Location: ../pages/forum.php');
        exit;
    }
    
    if ($action === 'accept') {
        // Vérifier si l'utilisateur est connecté
        if (!isset($_SESSION['user_id'])) {
            // Sauvegarder le token dans la session pour le traiter après la connexion
            $_SESSION['pending_invitation'] = $token;
            header('Location: ../pages/connexion.php');
            exit;
        }
        
        // Ajouter l'utilisateur au groupe
        $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id, joined_at) VALUES (?, ?, NOW())");
        $stmt->execute([$invitation['group_id'], $_SESSION['user_id']]);
        
        // Mettre à jour le statut de l'invitation
        $stmt = $pdo->prepare("UPDATE group_invitations SET status = 'accepted' WHERE token = ?");
        $stmt->execute([$token]);
        
        $_SESSION['message'] = "Vous avez rejoint le groupe avec succès !";
    } else if ($action === 'reject') {
        // Mettre à jour le statut de l'invitation
        $stmt = $pdo->prepare("UPDATE group_invitations SET status = 'rejected' WHERE token = ?");
        $stmt->execute([$token]);
        
        $_SESSION['message'] = "Vous avez refusé l'invitation.";
    }
    
} catch (Exception $e) {
    $_SESSION['message'] = "Une erreur est survenue lors du traitement de l'invitation.";
}

header('Location: ../pages/forum.php');
