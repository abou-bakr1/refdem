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
    $discussionId = $data['discussionId'] ?? null;
    
    if (!$discussionId) {
        throw new Exception('ID de discussion manquant');
    }
    
    $pdo = Connexion::pdo();
    
    // Vérifier que l'utilisateur est le propriétaire de la discussion
    $stmt = $pdo->prepare("SELECT user_id FROM discussions WHERE id = ?");
    $stmt->execute([$discussionId]);
    $discussion = $stmt->fetch();
    
    if (!$discussion || $discussion['user_id'] != $_SESSION['user_id']) {
        throw new Exception('Vous n\'avez pas les droits pour supprimer cette discussion');
    }
    
    $stmt = $pdo->prepare("DELETE FROM discussions WHERE id = ?");
    $stmt->execute([$discussionId]);
    
    $response = ['success' => true, 'message' => 'Discussion supprimée avec succès'];
    
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
