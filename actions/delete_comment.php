<?php
session_start();
require_once '../config/connexion.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté']);
    exit;
}

$response = ['success' => false, 'message' => ''];

try {
    $data = json_decode(file_get_contents('php://input'), true);
    $commentId = $data['commentId'] ?? null;
    
    if (!$commentId) {
        throw new Exception('ID du commentaire manquant');
    }
    
    $pdo = Connexion::pdo();
    
    // Vérifier que l'utilisateur est le propriétaire du commentaire
    $stmt = $pdo->prepare("SELECT user_id FROM comments WHERE id = ?");
    $stmt->execute([$commentId]);
    $comment = $stmt->fetch();
    
    if (!$comment || $comment['user_id'] != $_SESSION['user_id']) {
        throw new Exception('Vous n\'avez pas les droits pour supprimer ce commentaire');
    }
    
    $stmt = $pdo->prepare("DELETE FROM comments WHERE id = ?");
    $stmt->execute([$commentId]);
    
    $response = ['success' => true, 'message' => 'Commentaire supprimé avec succès'];
    
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
