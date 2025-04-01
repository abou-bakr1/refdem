<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

if (!isset($_POST['group_id']) || empty($_POST['group_id'])) {
    http_response_code(400);
    echo json_encode(['error' => 'ID du groupe manquant']);
    exit;
}

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if user is already a member
    $checkQuery = "SELECT COUNT(*) FROM group_members WHERE group_id = :group_id AND user_id = :user_id";
    $checkStmt = $db->prepare($checkQuery);
    $checkStmt->execute([
        'group_id' => $_POST['group_id'],
        'user_id' => $_SESSION['user_id']
    ]);
    
    if ($checkStmt->fetchColumn() > 0) {
        http_response_code(400);
        echo json_encode(['error' => 'Vous êtes déjà membre de ce groupe']);
        exit;
    }

    // Add user to group
    $query = "INSERT INTO group_members (group_id, user_id) VALUES (:group_id, :user_id)";
    $stmt = $db->prepare($query);
    $stmt->execute([
        'group_id' => $_POST['group_id'],
        'user_id' => $_SESSION['user_id']
    ]);

    echo json_encode(['success' => true, 'message' => 'Vous avez rejoint le groupe avec succès']);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]);
}