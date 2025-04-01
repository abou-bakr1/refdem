<?php
session_start();
require_once '../config/connexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour supprimer un groupe']);
    exit;
}

// Récupérer les données JSON
$data = json_decode(file_get_contents('php://input'), true);
$groupId = $data['groupId'] ?? null;

if (!$groupId) {
    echo json_encode(['success' => false, 'message' => 'ID du groupe manquant']);
    exit;
}

try {
    $pdo = Connexion::pdo();
    
    // Vérifier que l'utilisateur est bien l'admin du groupe
    $stmt = $pdo->prepare("SELECT admin_id FROM `groups` WHERE id = ?");
    $stmt->execute([$groupId]);
    $group = $stmt->fetch();
    
    if (!$group || $group['admin_id'] != $_SESSION['user_id']) {
        echo json_encode(['success' => false, 'message' => 'Vous n\'avez pas les droits pour supprimer ce groupe']);
        exit;
    }
    
    // Supprimer d'abord les membres du groupe
    $stmt = $pdo->prepare("DELETE FROM group_members WHERE group_id = ?");
    $stmt->execute([$groupId]);
    
    // Supprimer le groupe
    $stmt = $pdo->prepare("DELETE FROM `groups` WHERE id = ?");
    $stmt->execute([$groupId]);
    
    echo json_encode([
        'success' => true,
        'message' => 'Groupe supprimé avec succès'
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de la suppression du groupe']);
}
