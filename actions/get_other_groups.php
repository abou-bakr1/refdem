<?php
require_once '../config/database.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Non autorisé']);
    exit;
}

try {
    $db = new PDO($dsn, $username, $password);
    $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Get groups that the user hasn't joined yet
    $query = "SELECT g.*, u.username as creator_name, 
              (SELECT COUNT(*) FROM group_members WHERE group_id = g.id) as member_count 
              FROM `groups` g 
              JOIN users u ON g.creator_id = u.id 
              WHERE g.id NOT IN (
                  SELECT group_id FROM group_members WHERE user_id = :user_id
              )
              ORDER BY g.created_at DESC";
              
    $stmt = $db->prepare($query);
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $groups = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode(['success' => true, 'groups' => $groups]);
} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Erreur serveur: ' . $e->getMessage()]);
}