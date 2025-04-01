<?php
require_once '../config/connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $pdo = Connexion::pdo();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE mail = ?");
        $stmt->execute([$_POST['email']]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($_POST['password'], $user['mdp'])) {
            session_start();
            $_SESSION['user_id'] = $user['id'];
            header('Location: ../index.php');
            exit();
        }
    } catch(PDOException $e) {
        // Ne rien faire, on affichera le message d'erreur par défaut
    }
    
    // Si on arrive ici, c'est que soit la requête a échoué, soit les identifiants sont incorrects
    $error = "Email ou mot de passe incorrect";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Connexion</title>
    <link rel="stylesheet" href="../assets/css/connexion.css">
</head>
<body>
    <div class="gauche">
        <img src="../assets/img/logo.png" alt="LOGO">
        <p>Exprimez-vous,<br>faites la différence.<br>Votez dès maintenant !</p>
    </div>
    <div class="ligneV"></div>
    <div class="droite">
        <h1>Ça se passe<br>maintenant !</h1>
        <div class="identification">
            <form method="POST" action="">
                <?php if (isset($error)): ?>
                    <div class="alert"><?php echo htmlspecialchars($error); ?></div>
                <?php endif; ?>
                <input type="email" name="email" placeholder="MAIL" required>
                <input type="password" name="password" placeholder="MOT DE PASSE" required>
                <input type="submit" value="SE CONNECTER">
                <div class="ligneH"></div>
                <input type="button" value="S'INSCRIRE" onclick="window.location.href='inscription.php'">
            </form>
        </div>
    </div>
</body>
</html>
