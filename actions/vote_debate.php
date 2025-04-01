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
    $vote = $data['vote'] ?? null;
    
    if (!$debateId || !in_array($vote, ['pour', 'contre'])) {
        throw new Exception('Paramètres invalides');
    }
    
    $pdo = Connexion::pdo();
    
    // Vérifier si l'utilisateur a déjà voté
    $stmt = $pdo->prepare("SELECT id, vote FROM debate_votes WHERE debate_id = ? AND user_id = ?");
    $stmt->execute([$debateId, $_SESSION['user_id']]);
    $existingVote = $stmt->fetch();
    
    if ($existingVote) {
        if ($existingVote['vote'] === $vote) {
            // Supprimer le vote si l'utilisateur clique sur le même bouton
            $stmt = $pdo->prepare("DELETE FROM debate_votes WHERE id = ?");
            $stmt->execute([$existingVote['id']]);
        } else {
            // Mettre à jour le vote si l'utilisateur change d'avis
            $stmt = $pdo->prepare("UPDATE debate_votes SET vote = ? WHERE id = ?");
            $stmt->execute([$vote, $existingVote['id']]);
        }
    } else {
        // Créer un nouveau vote
        $stmt = $pdo->prepare("INSERT INTO debate_votes (debate_id, user_id, vote) VALUES (?, ?, ?)");
        $stmt->execute([$debateId, $_SESSION['user_id'], $vote]);
    }
    
    // Récupérer le nouveau compte des votes
    $stmt = $pdo->prepare("
        SELECT 
        (SELECT COUNT(*) FROM debate_votes WHERE debate_id = ? AND vote = 'pour') as votes_pour,
        (SELECT COUNT(*) FROM debate_votes WHERE debate_id = ? AND vote = 'contre') as votes_contre,
        (SELECT vote FROM debate_votes WHERE debate_id = ? AND user_id = ?) as user_vote
    ");
    $stmt->execute([$debateId, $debateId, $debateId, $_SESSION['user_id']]);
    $votes = $stmt->fetch();
    
    $response = [
        'success' => true,
        'votes_pour' => (int)$votes['votes_pour'],
        'votes_contre' => (int)$votes['votes_contre'],
        'user_vote' => $votes['user_vote']
    ];
    
} catch (Exception $e) {
    $response = ['success' => false, 'message' => $e->getMessage()];
}

echo json_encode($response);
