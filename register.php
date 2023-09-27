<?php
session_start();

if (isset($_SESSION['username'])) {
    // Si l'utilisateur est déjà connecté, rediriger vers la page d'accueil
    header("Location: home.php");
    exit();
}

require_once 'config.php';

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = $_POST["username"];
    $password = $_POST["password"];
    $email = $_POST["email"];
    $establishment = $_POST["establishment"];

    // Utilisation de requêtes préparées pour éviter les injections SQL
    $checkUserQuery = "SELECT * FROM users WHERE username = ?";
    $stmt = $conn->prepare($checkUserQuery);
    $stmt->bind_param("s", $username);
    $stmt->execute();
    $checkUserResult = $stmt->get_result();

    if ($checkUserResult->num_rows > 0) {
        $error = "Le nom d'utilisateur existe déjà.";
    } else {
        // Utilisation de requêtes préparées pour insérer un nouvel utilisateur
        $createUserQuery = "INSERT INTO users (username, password, email, establishment) VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($createUserQuery);
        $stmt->bind_param("ssss", $username, $password, $email, $establishment);

        if ($stmt->execute()) {
            $_SESSION['username'] = $username;
            header("Location: home.php");
            exit();
        } else {
            $error = "Erreur lors de la création de l'utilisateur: " . $conn->error;
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Inscription</title>
    <style>
        /* Votre CSS d'origine ici */
        body {
            background-color: white;
        }

        .username {
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .password {
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .email {
            display: flex;
            justify-content: center;
            align-items: center;
            color: white;
        }

        .container {
            width: 300px;
            padding: 30px;
            background-color: #ffffff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .title {
            text-align: center;
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="password"],
        input[type="email"] {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #dbdbdb;
            border-radius: 4px;
            background-color: #000000;
            color: white;
        }

        select {
            width: 100%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #dbdbdb;
            border-radius: 4px;
            background-color: #000000;
            color: white;
        }

        input[type="submit"] {
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
        }

        input[type="submit"]:hover {
            background-color: #1e5fa9;
            background-image: linear-gradient(to right, #006aff, #035bff);
        }

        .error-message {
            color: #ffffff;
            margin-top: 10px;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2 class="title">Inscription</h2>
        <form method="POST" action="<?php echo $_SERVER['PHP_SELF']; ?>">
            <label for="username">Nom d'utilisateur:</label>
            <input type="text" name="username" id="username" required class="username"><br><br>

            <label for="password">Mot de passe:</label>
            <input type="password" name="password" id="password" required class="password"><br><br>

            <label for="email">Adresse e-mail:</label>
            <input type="email" name="email" id="email" required class="email"><br><br>

            <label for="establishment">Établissement:</label>
            <select name="establishment" id="establishment" required>
                <option value="La_Mache">Lycée La Mache</option>
                <option value="L'oiselet">Lycée L'oiselet</option>
                <option value="Autre">Autre</option>
                <!-- Ajoutez d'autres options au besoin -->
            </select><br><br>

            <input type="submit" value="S'inscrire">
        </form>

        <a href="index.php"><button>Se connecter</button></a>
    </div>
    <?php if (isset($error)) { echo '<div class="error-message">' . $error . '</div>'; } ?>
</body>
</html>
<!---

██╗░░░░░██╗░░░██╗██╗░░██╗
██║░░░░░██║░░░██║╚██╗██╔╝
██║░░░░░██║░░░██║░╚███╔╝░
██║░░░░░██║░░░██║░██╔██╗░
███████╗╚██████╔╝██╔╝╚██╗
╚══════╝░╚═════╝░╚═╝░░╚═╝
--->