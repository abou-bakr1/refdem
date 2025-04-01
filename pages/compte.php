<?php
session_start();
require_once '../config/connexion.php';
require_once '../includes/functions.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['user_id'])) {
    header('Location: connexion.php');
    exit();
}

// Récupérer les informations de l'utilisateur
$user_id = $_SESSION['user_id'];
$pdo = Connexion::pdo();
$stmt = $pdo->prepare("SELECT prenom, mail as email, photo_profil FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

$page = 'compte';
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Accueil - Refdem</title>
    <link rel="stylesheet" href="../assets/css/header-footer.css">
    <link rel="stylesheet" href="../assets/css/compte.css">
</head>

<body>
    <?php include_once '../includes/header.php'; ?>

    <section class="profile-container">
        <h1>Bienvenue sur votre compte, <?php echo htmlspecialchars($user['prenom']); ?> !</h1>

        <form action="../actions/deconnexion.php" class="logout-form">
            <button type="submit" class="logout-btn">Se déconnecter</button>
        </form>

        <div class="profile-info">
            <div class="profile-section">
                <h3>Photo de profil</h3>
                <div class="profile-pic">
                    <img id="profile-image" src="<?php echo $user['photo_profil'] ? '../uploads/profile/' . $user['photo_profil'] : '../assets/images/default-profile.png'; ?>" alt="Photo de profil">
                    <div class="upload-container">
                        <label for="upload" class="upload-btn">Changer la photo</label>
                        <input type="file" id="upload" name="upload" accept="image/*">
                    </div>
                </div>
            </div>

            <div class="profile-section">
                <h3>Email</h3>
                <form class="email-section" action="../actions/update_email.php" method="post">
                    <div class="form-group">
                        <label for="current-email">Email actuel :</label>
                        <input type="email" id="current-email" name="current-email" value="<?php echo htmlspecialchars($user['email']); ?>" readonly>
                    </div>
                    <div class="form-group">
                        <label for="new-email">Nouvel email :</label>
                        <input type="email" id="new-email" name="new-email" placeholder="Nouveau email" required>
                    </div>
                    <button type="submit" class="update-btn">Modifier l'email</button>
                </form>
            </div>

            <div class="profile-section">
                <h3>Mot de passe</h3>
                <form class="password-section" action="../actions/update_password.php" method="post">
                    <div class="password-fields">
                        <div class="form-group">
                            <label for="current-password">Mot de passe actuel :</label>
                            <input type="password" id="current-password" name="current-password" placeholder="Mot de passe actuel" required>
                        </div>
                        <div class="form-group">
                            <label for="new-password">Nouveau mot de passe :</label>
                            <input type="password" id="new-password" name="new-password" placeholder="Nouveau mot de passe" required>
                        </div>
                        <div class="form-group">
                            <label for="confirm-password">Confirmation :</label>
                            <input type="password" id="confirm-password" name="confirm-password" placeholder="Confirmer le mot de passe" required>
                        </div>
                    </div>
                    <button type="submit" class="update-btn">Modifier le mot de passe</button>
                </form>
            </div>
        </div>
    </section>

    <?php include_once '../includes/footer.php'; ?>

    <script>
        document.getElementById('upload').addEventListener('change', (e) => {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    document.getElementById('profile-image').src = e.target.result;
                };
                reader.readAsDataURL(file);

                // Envoyer la photo au serveur
                const formData = new FormData();
                formData.append('photo', file);

                fetch('../actions/update_photo.php', {
                    method: 'POST',
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (!data.success) {
                        alert(data.message || 'Une erreur est survenue');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Une erreur est survenue');
                });
            }
        });
    </script>
</body>
</html>
