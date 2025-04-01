<?php
session_start();
require_once '../config/connexion.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode([]);
    exit;
}

$discussionId = $_GET['discussionId'] ?? null;

if (!$discussionId) {
    echo json_encode([]);
    exit;
}

try {
    $pdo = Connexion::pdo();
    
    $stmt = $pdo->prepare("
        SELECT c.*, u.prenom, u.nom, u.photo_profil 
        FROM comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.discussion_id = ? 
        ORDER BY c.created_at ASC
    ");
    $stmt->execute([$discussionId]);
    
    $comments = [];
    while ($row = $stmt->fetch()) {
        $comments[] = [
            'id' => $row['id'],
            'content' => htmlspecialchars($row['content']),
            'created_at' => date('d/m/Y H:i', strtotime($row['created_at'])),
            'user_name' => htmlspecialchars($row['prenom'] . ' ' . $row['nom']),
            'user_photo' => $row['photo_profil'] ? '../uploads/profile/' . $row['photo_profil'] : '../assets/images/default-profile.png',
            'is_owner' => $row['user_id'] == $_SESSION['user_id']
        ];
    }
    
    echo json_encode($comments);
    
} catch (Exception $e) {
    echo json_encode([]);
}
