<?php
session_start();

// Inclure le fichier de configuration
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: index.php");
    exit();
}

// Définir l'ID de l'utilisateur dont le profil doit être affiché (par défaut, l'utilisateur connecté)
$utilisateur_id_affiche = $_SESSION['id_utilisateur'];

// Vérifier s'il y a un paramètre d'ID d'utilisateur dans l'URL (ex: profil.php?id=5)
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    // Utilisation de l'ID spécifié dans l'URL
    $utilisateur_id_affiche = intval($_GET['id']);
}

// Récupérer les informations de l'utilisateur à partir de la base de données
$sql = "SELECT username, photo_profil, biographie FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $utilisateur_id_affiche);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $nomUtilisateur = $row['username'];
    $photoProfil = $row['photo_profil'];
    $biographie = $row['biographie'];
} else {
    // L'utilisateur n'a pas été trouvé
    echo "Utilisateur non trouvé.";
}

// Vérifier si l'utilisateur consulte son propre profil
$estSonProfil = ($_SESSION['id_utilisateur'] === $utilisateur_id_affiche);

// Récupérer les messages de l'utilisateur depuis la base de données
$sql_messages = "SELECT contenu FROM messages WHERE utilisateur_id = ? ORDER BY date_creation DESC";
$stmt_messages = $conn->prepare($sql_messages);
$stmt_messages->bind_param("i", $utilisateur_id_affiche);
$stmt_messages->execute();
$result_messages = $stmt_messages->get_result();

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil de <?php echo $nomUtilisateur; ?></title>
    <style>
    /* Styles pour la page de profil */
    body {
        font-family: Arial, sans-serif;
        background-color: #f0f0f0;
        margin: 0;
        padding: 0;
    }

    .profil {
        background-color: #fff;
        padding: 20px;
        text-align: center;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin: 20px auto;
        max-width: 600px;
    }

    .profil img {
        width: 150px;
        height: 150px;
        border-radius: 50%;
        object-fit: cover;
        margin-bottom: 10px;
    }

    .profil h1 {
        font-size: 24px;
        margin: 0;
    }

    .profil p {
        font-size: 16px;
        color: #777;
        margin: 10px 0;
    }

    .messages {
        background-color: #fff;
        padding: 20px;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        margin: 20px auto;
        max-width: 600px;
    }

    .messages h2 {
        font-size: 20px;
        margin: 0;
        margin-bottom: 10px;
    }

    .messages p {
        font-size: 16px;
        margin: 10px 0;
    }

    a {
        display: block;
        text-align: center;
        background-color: #007bff;
        color: #fff;
        text-decoration: none;
        padding: 10px 20px;
        margin: 10px auto;
        max-width: 200px;
        border-radius: 5px;
    }

    a:hover {
        background-color: #0056b3;
    }

    /* Media Queries pour la responsivité */

    /* Pour les écrans plus petits que 600px de large */
    @media (max-width: 600px) {
        .profil img {
            width: 100px;
            height: 100px;
            margin-bottom: 5px;
        }

        .profil h1 {
            font-size: 20px;
        }

        .profil p {
            font-size: 14px;
        }

        .messages h2 {
            font-size: 18px;
        }

        .messages p {
            font-size: 14px;
        }

        a {
            max-width: 150px;
            padding: 8px 16px;
        }
    }
    </style>
</head>
<body>

<div class="profil">
    <img src="<?php echo $photoProfil; ?>" alt="Photo de profil de <?php echo $nomUtilisateur; ?>">
    <h1><?php echo $nomUtilisateur; ?></h1>
    <p><?php echo $biographie; ?></p>
    
    <?php
    // Afficher le bouton "Envoyer un message" uniquement si l'utilisateur consulte un autre profil
    if (!$estSonProfil) {
        echo '<a href="chat.php?id=' . $utilisateur_id_affiche . '">Envoyer un message</a>';
    }
    ?>
</div>

<div class="messages">
    <h2>Messages de <?php echo $nomUtilisateur; ?></h2>
    <?php
    if ($result_messages->num_rows > 0) {
        while ($message_row = $result_messages->fetch_assoc()) {
            $contenuMessage = htmlspecialchars($message_row['contenu']);
            echo "<p>$contenuMessage</p>";
        }
    } else {
        echo "<p>Aucun message n'a été publié.</p>";
    }
    ?>
</div>

<?php
// Afficher le bouton "Modifier le profil" uniquement si l'utilisateur consulte son propre profil
if ($estSonProfil) {
    echo '<a href="modifier.php">Modifier le profil</a>';
}
?>

<a href="home.php">Retourner au menu</a>
<a href="logout.php">Déconnexion</a>

</body>
</html>
