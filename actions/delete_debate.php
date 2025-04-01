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
    $debateId = $data['debateId'] ?? null;
    
    if (!$debateId) {
        throw new Exception('ID du débat manquant');
    }
    
    $pdo = Connexion::pdo();
    
    // Vérifier que l'utilisateur est le propriétaire du débat
    $stmt = $pdo->prepare("SELECT user_id FROM debates WHERE id = ?");
    $stmt->execute([$debateId]);
    $debate = $stmt->fetch();
    
    if (!$debate || $debate['user_id'] != $_SESSION['user_id']) {
        throw new Exception('Vous n\'avez pas les droits pour supprimer ce débat');
    }
    
    $stmt = $pdo->prepare("DELETE FROM debates WHERE id = ?");
    $stmt->execute([$debateId]);
    
    $response = ['success' => true, 'message' => 'Débat supprimé avec succès'];
    
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
