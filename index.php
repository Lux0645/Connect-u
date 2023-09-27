<?php
session_start();

// Inclure le fichier de configuration
require_once 'config.php';

// Vérifier si l'utilisateur est déjà connecté, le rediriger vers la page d'accueil
if (isset($_SESSION['id_utilisateur'])) {
    header("Location: home.php");
    exit();
}

$error = '';

// Traitement de la soumission du formulaire de connexion
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nom_utilisateur = $_POST["nom_utilisateur"];
    $mot_de_passe = $_POST["mot_de_passe"];

    // Validation côté serveur
    if (empty($nom_utilisateur) || empty($mot_de_passe)) {
        $error = "Veuillez remplir tous les champs.";
    } else {
        // Utilisation de requêtes préparées pour éviter les injections SQL
        $sql = "SELECT * FROM users WHERE username = ? AND password = ? LIMIT 1";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ss", $nom_utilisateur, $mot_de_passe);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            // Connexion réussie, enregistrer l'ID utilisateur en session
            $_SESSION['id_utilisateur'] = $row['id'];
            $_SESSION['nom_utilisateur'] = $row['username'];

            // Créer un cookie avec le nom d'utilisateur et définir sa durée de vie à 24 heures
            $cookie_nom_utilisateur = $row['username'];
            setcookie('nom_utilisateur', $cookie_nom_utilisateur, time() + 86400, '/'); // 86400 secondes = 24 heures

            // Régénération de l'ID de session
            session_regenerate_id(true);

            header("Location: index.php");
            exit();
        } else {
            $error = "Nom d'utilisateur ou mot de passe incorrect.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Connexion</title>
    <!-- Inclusion de Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        /* Votre CSS d'origine ici */
        body {
            background-color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
        }

        .login-form {
            width: 300px;
            padding: 30px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            font-weight: bold;
        }

        .login-form h2 {
            text-align: center;
            margin-bottom: 30px;
            font-weight: bold;
        }

        .form-group {
            margin-bottom: 20px;
            font-weight: bold;
        }

        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        .form-group input[type="text"],
        .form-group input[type="password"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #dbdbdb;
            border-radius: 4px;
            background-color: #000000;
            color: white;
            font-weight: bold;
        }

        .form-group input[type="submit"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            background-color: #0e0efc;
            background-image: linear-gradient(to right, #0059ff, #006aff);
            border: none;
            color: #ffffff;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            font-weight: bold;
        }

        .form-group input[type="submit"]:hover {
            background-color: #1e5fa9;
            background-image: linear-gradient(to right, #006aff, #035bff);
        }

        .form-group .inscr {
            display: block;
            text-align: center;
            margin-top: 10px;
            text-decoration: none;
            color: #ffffff;
            font-weight: bold;
            background-color: #0e0efc;
            background-image: linear-gradient(to right, #0059ff, #006aff);
            padding: 10px;
            border-radius: 4px;
            transition: background-color 0.3s ease;
        }

        .form-group .inscr:hover {
            background-color: #1e5fa9;
            background-image: linear-gradient(to right, #006aff, #035bff);
        }

        .error-message {
            color: #ffffff;
            margin-top: 10px;
            text-align: center;
        }
        .inscr {
          font-family: "Arial", sans-serif;
          /* font-size: 24px; Taille de la police*/
          /* font-weight: bold; Gras */
        }
    </style>
</head>
<body>
    <h2>Connexion</h2>
    <form method="POST" class="login-form">
        <div class="form-group">
            <label for="nom_utilisateur">Nom d'utilisateur:</label>
            <input type="text" name="nom_utilisateur" id="nom_utilisateur" required>
        </div>
        
        <div class="form-group">
            <label for="mot_de_passe">Mot de passe:</label>
            <input type="password" name="mot_de_passe" id="mot_de_passe" required>
        </div>
        
        <div class="form-group">
            <input type="submit" value="Se connecter" class="btn btn-success">
        </div>
        
        <div class="form-group">
            <a href="register.php" class="inscr btn btn-primary">S'inscrire</a>
        </div>
        <div class="form-group">
            <a href="log.html" class="inscr btn btn-info">Logs</a>
        </div>
        
    </form>
    
    <?php if(!empty($error)) { echo '<div class="error-message">' . $error . '</div>'; } ?>
</body>
</html>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>