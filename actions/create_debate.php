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
    $description = $_POST['description'] ?? '';
    $argumentPour = $_POST['argumentPour'] ?? '';
    $argumentContre = $_POST['argumentContre'] ?? '';
    $theme = $_POST['theme'] ?? '';
    
    if (!$groupId || !$sujet || !$description || !$argumentPour || !$argumentContre || !$theme) {
        throw new Exception('Tous les champs sont obligatoires');
    }
    
    // Vérifier que l'utilisateur est membre du groupe
    $stmt = $pdo->prepare("SELECT id FROM groups WHERE id = ? AND (admin_id = ? OR id IN (SELECT group_id FROM group_members WHERE user_id = ?))");
    $stmt->execute([$groupId, $_SESSION['user_id'], $_SESSION['user_id']]);
    if (!$stmt->fetch()) {
        throw new Exception('Vous n\'avez pas les droits pour créer un débat dans ce groupe');
    }
    
    $stmt = $pdo->prepare("INSERT INTO debates (group_id, user_id, sujet, description, argument_pour, argument_contre, theme) VALUES (?, ?, ?, ?, ?, ?, ?)");
    $stmt->execute([$groupId, $_SESSION['user_id'], $sujet, $description, $argumentPour, $argumentContre, $theme]);
    
    $debateId = $pdo->lastInsertId();
    
    // Récupérer les informations complètes du débat
    $stmt = $pdo->prepare("
        SELECT d.*, u.prenom, u.nom, u.photo_profil,
        (SELECT COUNT(*) FROM debate_votes WHERE debate_id = d.id AND vote = 'pour') as votes_pour,
        (SELECT COUNT(*) FROM debate_votes WHERE debate_id = d.id AND vote = 'contre') as votes_contre
        FROM debates d
        JOIN users u ON d.user_id = u.id
        WHERE d.id = ?
    ");
    $stmt->execute([$debateId]);
    $debate = $stmt->fetch();
    
    $response = [
        'success' => true,
        'debate' => [
            'id' => $debate['id'],
            'sujet' => htmlspecialchars($debate['sujet']),
            'description' => htmlspecialchars($debate['description']),
            'argument_pour' => htmlspecialchars($debate['argument_pour']),
            'argument_contre' => htmlspecialchars($debate['argument_contre']),
            'theme' => htmlspecialchars($debate['theme']),
            'created_at' => date('d/m/Y H:i', strtotime($debate['created_at'])),
            'user_name' => htmlspecialchars($debate['prenom'] . ' ' . $debate['nom']),
            'user_photo' => $debate['photo_profil'] ? '../uploads/profile/' . $debate['photo_profil'] : '../assets/images/default-profile.png',
            'is_owner' => true,
            'votes_pour' => (int)$debate['votes_pour'],
            'votes_contre' => (int)$debate['votes_contre'],
            'user_vote' => null
        ]
    ];
    
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
