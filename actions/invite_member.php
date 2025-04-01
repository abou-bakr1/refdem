<?php
session_start();
require_once '../config/connexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour inviter un membre']);
    exit;
}

if (!isset($_POST['inviteEmail']) || !isset($_POST['groupId'])) {
    echo json_encode(['success' => false, 'message' => 'Données manquantes']);
    exit;
}

$email = filter_var($_POST['inviteEmail'], FILTER_VALIDATE_EMAIL);
$groupId = intval($_POST['groupId']);

if (!$email) {
    echo json_encode(['success' => false, 'message' => 'Adresse email invalide']);
    exit;
}

try {
    $pdo = Connexion::pdo();
    
    // Vérifier si l'utilisateur est l'admin du groupe
    $stmt = $pdo->prepare("SELECT * FROM `groups` WHERE id = ? AND admin_id = ?");
    $stmt->execute([$groupId, $_SESSION['user_id']]);
    $group = $stmt->fetch();
    
    if (!$group) {
        echo json_encode(['success' => false, 'message' => 'Vous n\'avez pas les droits pour inviter des membres dans ce groupe']);
        exit;
    }
    
    // Vérifier si l'invitation existe déjà
    $stmt = $pdo->prepare("SELECT * FROM group_invitations WHERE email = ? AND group_id = ? AND status = 'pending'");
    $stmt->execute([$email, $groupId]);
    if ($stmt->fetch()) {
        echo json_encode(['success' => false, 'message' => 'Une invitation a déjà été envoyée à cette adresse email']);
        exit;
    }
    
    // Générer un token unique pour l'invitation
    $token = bin2hex(random_bytes(32));
    
    // Enregistrer l'invitation
    $stmt = $pdo->prepare("INSERT INTO group_invitations (group_id, email, token, status, created_at) VALUES (?, ?, ?, 'pending', NOW())");
    $stmt->execute([$groupId, $email, $token]);
    
    // Envoyer l'email d'invitation
    $acceptLink = "http://localhost/refdem/actions/handle_invitation.php?action=accept&token=" . $token;
    $rejectLink = "http://localhost/refdem/actions/handle_invitation.php?action=reject&token=" . $token;
    
    $to = $email;
    $subject = "Invitation à rejoindre un groupe sur ReForum";
    $message = "
    <html>
    <head>
        <title>Invitation à rejoindre un groupe</title>
    </head>
    <body>
        <h2>Vous avez été invité à rejoindre un groupe sur ReForum</h2>
        <p>Pour accepter l'invitation, cliquez sur ce lien : <a href='$acceptLink'>Accepter l'invitation</a></p>
        <p>Pour refuser l'invitation, cliquez sur ce lien : <a href='$rejectLink'>Refuser l'invitation</a></p>
    </body>
    </html>
    ";
    
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
    $headers .= 'From: ReForum <no-reply@reforum.com>' . "\r\n";
    
    mail($to, $subject, $message, $headers);
    
    echo json_encode(['success' => true, 'message' => 'Invitation envoyée avec succès']);
    
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de l\'envoi de l\'invitation']);
}
