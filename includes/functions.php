<?php

/**
 * Nettoie une chaîne de caractères
 * @param string $string La chaîne à nettoyer
 * @return string La chaîne nettoyée
 */
function clean_string($string) {
    $string = strip_tags($string);
    $string = trim($string);
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Vérifie si l'utilisateur est connecté
 * @return bool True si l'utilisateur est connecté, false sinon
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Redirige vers une page
 * @param string $page La page vers laquelle rediriger
 */
function redirect($page) {
    header("Location: $page");
    exit();
}

/**
 * Génère un nom de fichier unique pour une image
 * @param string $original_name Le nom original du fichier
 * @return string Le nouveau nom du fichier
 */
function generate_unique_filename($original_name) {
    $extension = pathinfo($original_name, PATHINFO_EXTENSION);
    return uniqid() . '.' . $extension;
}

/**
 * Vérifie si un fichier est une image valide
 * @param array $file Le fichier à vérifier ($_FILES['key'])
 * @return bool True si le fichier est une image valide, false sinon
 */
function is_valid_image($file) {
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
    $max_size = 5 * 1024 * 1024; // 5MB

    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return false;
    }

    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    return in_array($mime_type, $allowed_types) && $file['size'] <= $max_size;
}

/**
 * Vérifie si un utilisateur est admin d'un groupe
 * @param PDO $pdo Instance PDO de la base de données
 * @param int $user_id ID de l'utilisateur
 * @param int $group_id ID du groupe
 * @return bool True si l'utilisateur est admin du groupe, false sinon
 */
function is_group_admin($pdo, $user_id, $group_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM groups WHERE id = ? AND admin_id = ?");
    $stmt->execute([$group_id, $user_id]);
    return $stmt->fetchColumn() > 0;
}

/**
 * Vérifie si un utilisateur est membre d'un groupe
 * @param PDO $pdo Instance PDO de la base de données
 * @param int $user_id ID de l'utilisateur
 * @param int $group_id ID du groupe
 * @return bool True si l'utilisateur est membre du groupe, false sinon
 */
function is_group_member($pdo, $user_id, $group_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM group_members WHERE group_id = ? AND user_id = ?");
    $stmt->execute([$group_id, $user_id]);
    return $stmt->fetchColumn() > 0;
}

/**
 * Obtient les informations d'un utilisateur
 * @param PDO $pdo Instance PDO de la base de données
 * @param int $user_id ID de l'utilisateur
 * @return array|false Les informations de l'utilisateur ou false si non trouvé
 */
function get_user_info($pdo, $user_id) {
    $stmt = $pdo->prepare("SELECT id, prenom, nom, email, photo_profil FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

/**
 * Obtient le nombre de membres d'un groupe
 * @param PDO $pdo Instance PDO de la base de données
 * @param int $group_id ID du groupe
 * @return int Le nombre de membres
 */
function get_group_member_count($pdo, $group_id) {
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM group_members WHERE group_id = ?");
    $stmt->execute([$group_id]);
    return $stmt->fetchColumn();
}
