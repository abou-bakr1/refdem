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
        SELECT d.*, u.prenom, u.nom, u.photo_profil 
        FROM discussions d 
        JOIN users u ON d.user_id = u.id 
        WHERE d.group_id = ? 
        ORDER BY d.created_at DESC
    ");
    $stmt->execute([$groupId]);
    
    $discussions = [];
    while ($row = $stmt->fetch()) {
        $discussions[] = [
            'id' => $row['id'],
            'sujet' => htmlspecialchars($row['sujet']),
            'commentaire' => htmlspecialchars($row['commentaire']),
            'theme' => htmlspecialchars($row['theme']),
            'created_at' => date('d/m/Y H:i', strtotime($row['created_at'])),
            'user_name' => htmlspecialchars($row['prenom'] . ' ' . $row['nom']),
            'user_photo' => $row['photo_profil'] ? '../uploads/profile/' . $row['photo_profil'] : '../assets/images/default-profile.png',
            'is_owner' => $row['user_id'] == $_SESSION['user_id']
        ];
    }
    
    echo json_encode($discussions);
    
} catch (Exception $e) {
    echo json_encode([]);
}
