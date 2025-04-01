<?php
session_start();
require_once '../config/connexion.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté']);
    exit;
}

$response = ['success' => false, 'message' => ''];

try {
    $pdo = Connexion::pdo();
    
    $groupId = $_POST['groupId'] ?? null;
    $sujet = $_POST['sujet'] ?? '';
    $commentaire = $_POST['commentaire'] ?? '';
    $theme = $_POST['theme'] ?? '';
    
    if (!$groupId || !$sujet || !$commentaire || !$theme) {
        throw new Exception('Tous les champs sont obligatoires');
    }
    
    // Vérifier que l'utilisateur est membre du groupe
    $stmt = $pdo->prepare("SELECT id FROM groups WHERE id = ? AND (admin_id = ? OR id IN (SELECT group_id FROM group_members WHERE user_id = ?))");
    $stmt->execute([$groupId, $_SESSION['user_id'], $_SESSION['user_id']]);
    if (!$stmt->fetch()) {
        throw new Exception('Vous n\'avez pas les droits pour créer une discussion dans ce groupe');
    }
    
    $stmt = $pdo->prepare("INSERT INTO discussions (group_id, user_id, sujet, commentaire, theme) VALUES (?, ?, ?, ?, ?)");
    $stmt->execute([$groupId, $_SESSION['user_id'], $sujet, $commentaire, $theme]);
    
    $response = ['success' => true, 'message' => 'Discussion créée avec succès'];
    
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
