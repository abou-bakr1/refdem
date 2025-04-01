<?php
session_start();
require_once '../config/connexion.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$groupId = $_GET['groupId'] ?? null;

if (!$groupId) {
    echo json_encode([]);
    exit;
}

try {
    $pdo = Connexion::pdo();
    
    $stmt = $pdo->prepare("
        SELECT d.*, u.prenom, u.nom, u.photo_profil,
        (SELECT COUNT(*) FROM debate_votes WHERE debate_id = d.id AND vote = 'pour') as votes_pour,
        (SELECT COUNT(*) FROM debate_votes WHERE debate_id = d.id AND vote = 'contre') as votes_contre,
        (SELECT vote FROM debate_votes WHERE debate_id = d.id AND user_id = ?) as user_vote
        FROM debates d 
        JOIN users u ON d.user_id = u.id 
        WHERE d.group_id = ? 
        ORDER BY d.created_at DESC
    ");
    $stmt->execute([$_SESSION['user_id'], $groupId]);
    
    $debates = [];
    while ($row = $stmt->fetch()) {
        $debates[] = [
            'id' => $row['id'],
            'sujet' => htmlspecialchars($row['sujet']),
            'description' => htmlspecialchars($row['description']),
            'argument_pour' => htmlspecialchars($row['argument_pour']),
            'argument_contre' => htmlspecialchars($row['argument_contre']),
            'theme' => htmlspecialchars($row['theme']),
            'created_at' => date('d/m/Y H:i', strtotime($row['created_at'])),
            'user_name' => htmlspecialchars($row['prenom'] . ' ' . $row['nom']),
            'user_photo' => $row['photo_profil'] ? '../uploads/profile/' . $row['photo_profil'] : '../assets/images/default-profile.png',
            'is_owner' => $row['user_id'] == $_SESSION['user_id'],
            'votes_pour' => (int)$row['votes_pour'],
            'votes_contre' => (int)$row['votes_contre'],
            'user_vote' => $row['user_vote']
        ];
    }
    
    echo json_encode($debates);
    
} catch (Exception $e) {
    echo json_encode([]);
}
