<?php
// Définir le chemin de base en fonction de la page actuelle
$base_path = isset($page) && $page !== 'index' ? '../' : '';
?>
<div class="menu">
    <img src="<?php echo $base_path; ?>assets/img/logo.png" alt="LOGO" class="logo">
    <div class="bar">
        <form action="<?php echo $base_path; ?>index.php" method="GET">
            <input type="submit" value="ACCUEIL" <?php echo (!isset($page) || $page === 'index') ? 'class="active"' : ''; ?>>
        </form>
        <form action="<?php echo $base_path; ?>pages/forum.php" method="GET">
            <input type="submit" value="FORUM" <?php echo (isset($page) && $page === 'forum') ? 'class="active"' : ''; ?>>
        </form>
        <form action="<?php echo $base_path; ?>pages/compte.php" method="GET">
            <input type="submit" value="MON COMPTE" <?php echo (isset($page) && $page === 'compte') ? 'class="active"' : ''; ?>>
        </form>
    </div>
</div>
