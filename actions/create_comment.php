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
    
    $discussionId = $_POST['discussionId'] ?? null;
    $content = $_POST['content'] ?? '';
    
    if (!$discussionId || !$content) {
        throw new Exception('Le commentaire ne peut pas être vide');
    }
    
    $stmt = $pdo->prepare("INSERT INTO comments (discussion_id, user_id, content) VALUES (?, ?, ?)");
    $stmt->execute([$discussionId, $_SESSION['user_id'], $content]);
    
    // Récupérer les informations du commentaire créé
    $commentId = $pdo->lastInsertId();
    $stmt = $pdo->prepare("
        SELECT c.*, u.prenom, u.nom, u.photo_profil 
        FROM comments c 
        JOIN users u ON c.user_id = u.id 
        WHERE c.id = ?
    ");
    $stmt->execute([$commentId]);
    $comment = $stmt->fetch();
    
    $response = [
        'success' => true,
        'comment' => [
            'id' => $comment['id'],
            'content' => htmlspecialchars($comment['content']),
            'created_at' => date('d/m/Y H:i', strtotime($comment['created_at'])),
            'user_name' => htmlspecialchars($comment['prenom'] . ' ' . $comment['nom']),
            'user_photo' => $comment['photo_profil'] ? '../uploads/profile/' . $comment['photo_profil'] : '../assets/images/default-profile.png',
            'is_owner' => true
        ]
    ];
    
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
