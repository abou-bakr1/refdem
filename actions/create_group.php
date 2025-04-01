<?php
session_start();
require_once '../config/connexion.php';

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Vous devez être connecté pour créer un groupe']);
    exit;
}

if (!isset($_POST['groupName']) || empty(trim($_POST['groupName']))) {
    echo json_encode(['success' => false, 'message' => 'Le nom du groupe est requis']);
    exit;
}

$groupName = trim($_POST['groupName']);
$admin_id = $_SESSION['user_id'];

try {
    $pdo = Connexion::pdo();
    
    // Vérifier si le nom du groupe existe déjà
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM `groups` WHERE name = ?");
    $stmt->execute([$groupName]);
    
    if ($stmt->fetchColumn() > 0) {
        echo json_encode(['success' => false, 'message' => 'Un groupe avec ce nom existe déjà']);
        exit;
    }
    
    // Créer le groupe
    $stmt = $pdo->prepare("INSERT INTO `groups` (name, admin_id, created_at) VALUES (?, ?, NOW())");
    $stmt->execute([$groupName, $admin_id]);
    
    $group_id = $pdo->lastInsertId();
    
    // Ajouter l'admin comme membre du groupe
    $stmt = $pdo->prepare("INSERT INTO group_members (group_id, user_id, joined_at) VALUES (?, ?, NOW())");
    $stmt->execute([$group_id, $admin_id]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Groupe créé avec succès !',
        'groupName' => $groupName
    ]);
    
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'Une erreur est survenue lors de la création du groupe']);
}
