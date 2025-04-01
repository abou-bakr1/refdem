<?php
class Connexion {
    // Les attributs static caractéristiques de la connexion
    static private $hostname = 'localhost';
    static private $database = 'aazabar';        // votre id court
    static private $login = 'aazabar';           // votre id court
    static private $password = 'd4C4G_.E(O|_SVM8$eX)';   // votre mdp

    static private $tabUTF8 = array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8");

    // L'attribut static qui matérialisera la connexion
    static private $pdo;

    // Le getter public de cet attribut
    static public function pdo() {
        if (!self::$pdo) {
            self::connect();
        }
        return self::$pdo;
    }

    // La fonction static de connexion qui initialise $pdo et lance la tentative de connexion
    static public function connect() {
        if (self::$pdo) {
            return true;
        }

        try {
            $dsn = "mysql:host=" . self::$hostname . ";dbname=" . self::$database . ";charset=utf8mb4";
            self::$pdo = new PDO($dsn, self::$login, self::$password);
            
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            
            return true;
        } catch (PDOException $e) {
            error_log("Erreur de connexion à la base de données: " . $e->getMessage());
            throw new Exception("Une erreur est survenue lors de la connexion à la base de données.");
        }
    }
}

// Établir la connexion dès l'inclusion du fichier
Connexion::connect();
?>
