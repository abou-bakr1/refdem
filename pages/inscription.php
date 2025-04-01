<?php
require_once '../config/connexion.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nom = $_POST['nom'] ?? '';
    $prenom = $_POST['prenom'] ?? '';
    $adresse = $_POST['adresse'] ?? '';
    $mail = $_POST['mail'] ?? '';
    $mdp = $_POST['mdp'] ?? '';
    
    // Validation des données
    $errors = [];
    
    if (empty($nom)) $errors[] = "Le nom est requis";
    if (empty($prenom)) $errors[] = "Le prénom est requis";
    if (empty($adresse)) $errors[] = "L'adresse est requise";
    if (empty($mail)) $errors[] = "L'email est requis";
    if (empty($mdp)) $errors[] = "Le mot de passe est requis";
    
    if (!filter_var($mail, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "L'adresse email n'est pas valide";
    }
    
    if (empty($errors)) {
        try {
            $pdo = Connexion::pdo();
            
            // Vérifier si l'email existe déjà
            $stmt = $pdo->prepare("SELECT id FROM users WHERE mail = ?");
            $stmt->execute([$mail]);
            if ($stmt->rowCount() > 0) {
                $errors[] = "Cette adresse email est déjà utilisée";
            } else {
                // Hash du mot de passe
                $hashed_password = password_hash($mdp, PASSWORD_DEFAULT);
                
                // Insertion dans la base de données
                $stmt = $pdo->prepare("INSERT INTO users (nom, prenom, adresse, mail, mdp) VALUES (?, ?, ?, ?, ?)");
                if ($stmt->execute([$nom, $prenom, $adresse, $mail, $hashed_password])) {
                    // Redirection vers la page de connexion avec message de succès
                    header("Location: connexion.php?success=1");
                    exit();
                } else {
                    $errors[] = "Erreur lors de l'inscription";
                }
            }
        } catch (PDOException $e) {
            $errors[] = "Une erreur est survenue lors de l'inscription";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inscription</title>
    <link rel="stylesheet" href="../assets/css/inscription.css">
</head>
<body>
    <div class="gauche">
        <img src="../assets/img/logo.png" alt="LOGO">
        <h2 class="txtGauche">Exprimez-vous,<br>faites la différence.<br>Votez dès maintenant !</h2>
    </div>
    <div class="ligne"></div>
    <div class="droite">
        <h1>Ça se passe<br>maintenant !</h1>
        <div class="identification">
            <?php if (!empty($errors)): ?>
                <div class="errors">
                    <?php foreach ($errors as $error): ?>
                        <p class="error"><?php echo htmlspecialchars($error); ?></p>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            <form method="POST" action="">
                <input type="text" name="nom" placeholder="NOM" required value="<?php echo htmlspecialchars($nom ?? ''); ?>">
                <input type="text" name="prenom" placeholder="PRENOM" required value="<?php echo htmlspecialchars($prenom ?? ''); ?>">
                <input type="text" name="adresse" placeholder="ADRESSE POSTALE" required value="<?php echo htmlspecialchars($adresse ?? ''); ?>">
                <input type="email" name="mail" placeholder="MAIL" required value="<?php echo htmlspecialchars($mail ?? ''); ?>">
                <input type="password" name="mdp" placeholder="MOT DE PASSE" required>
                <input type="submit" value="S'INSCRIRE">
                <h2>
                    En cliquant sur S'inscrire, vous acceptez nos Conditions générales. Découvrez
                    comment nous recueillons, utilisons et partageons vos données en lisant notre
                    Politique de confidentialité.
                </h2>
            </form>
        </div>
    </div>
</body>
</html>