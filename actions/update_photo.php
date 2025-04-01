<?php
session_start();
require_once '../config/connexion.php';
require_once '../includes/functions.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'Utilisateur non connecté']);
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['photo'])) {
    $user_id = $_SESSION['user_id'];
    $file = $_FILES['photo'];
    $response = ['success' => false];
    $pdo = Connexion::pdo();

    // Vérifier le type de fichier
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    if (!in_array($file['type'], $allowed_types)) {
        echo json_encode(['success' => false, 'message' => 'Type de fichier non autorisé']);
        exit();
    }

    // Vérifier la taille du fichier (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        echo json_encode(['success' => false, 'message' => 'Le fichier est trop volumineux']);
        exit();
    }

    // Créer le dossier uploads s'il n'existe pas
    $upload_dir = '../uploads/profile/';
    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Générer un nom de fichier unique
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid() . '.' . $extension;
    $filepath = $upload_dir . $filename;

    // Déplacer le fichier
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        try {
            // Supprimer l'ancienne photo si elle existe
            $stmt = $pdo->prepare("SELECT photo_profil FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            $old_photo = $stmt->fetchColumn();

            if ($old_photo && file_exists($upload_dir . $old_photo)) {
                unlink($upload_dir . $old_photo);
            }

            // Mettre à jour la base de données
            $stmt = $pdo->prepare("UPDATE users SET photo_profil = ? WHERE id = ?");
            $stmt->execute([$filename, $user_id]);

            echo json_encode([
                'success' => true,
                'photo_url' => '../uploads/profile/' . $filename
            ]);
        } catch (PDOException $e) {
            echo json_encode([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour de la base de données'
            ]);
        }
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'Erreur lors du téléchargement du fichier'
        ]);
    }
} else {
    echo json_encode([
        'success' => false,
        'message' => 'Aucun fichier reçu'
    ]);
}
