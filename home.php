<?php
session_start();

// Inclure le fichier de configuration
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: index.php");
    exit();
}

// Récupérer le nom d'utilisateur à partir des cookies
if (isset($_COOKIE['nom_utilisateur'])) {
    $nom_utilisateur = $_COOKIE['nom_utilisateur'];
} else {
    // Gérer le cas où le cookie n'existe pas (peut-être déconnecté)
    $nom_utilisateur = 'Utilisateur inconnu';
}

// Vérifier si le nom d'utilisateur est autorisé à supprimer des messages
if ($nom_utilisateur === 'admin' || $nom_utilisateur === 'Noctalivip') {
    // Vérifier si le formulaire de suppression a été soumis
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'supprimer') {
        // Supprimer le message de la base de données
        $message_id = $_POST['message_id'];
        $sql = "DELETE FROM messages WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $message_id);

        if ($stmt->execute()) {
            // Le message a été supprimé avec succès
            header('Location: home.php'); // Rediriger vers la page du chat après la suppression
            exit();
        } else {
            // Une erreur s'est produite lors de la suppression du message
            echo "Erreur lors de la suppression du message.";
        }
    }
}

// Vérifier si le formulaire d'envoi de message a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    // Récupérer le contenu du message depuis le formulaire
    $contenuMessage = $_POST['message'];

    // Créer le message à insérer dans la base de données, en incluant le nom d'utilisateur
    $message_a_inserer = $nom_utilisateur . ': ' . $contenuMessage;

    // Insérer le message dans la base de données
    $utilisateur_id = $_SESSION['id_utilisateur'];
    $sql = "INSERT INTO messages (utilisateur_id, contenu) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $utilisateur_id, $message_a_inserer);

    if ($stmt->execute()) {
        // Le message a été enregistré avec succès
        header('Location: home.php'); // Rediriger vers la page du chat après l'envoi du message
        exit();
    } else {
        // Une erreur s'est produite lors de l'enregistrement du message
        echo "Erreur lors de l'enregistrement du message.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat</title>

    <!-- Styles CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <style>
        /* Styles CSS pour le thème clair */
        body.light-theme {
            background-color: white;
            color: black;
        }

        /* Styles CSS pour le thème sombre */
        body.dark-theme {
            background-color: #333;
            color: white;
        }

        /* Styles CSS pour le bouton de bascule de thème */
        #theme-toggle {
            padding: 10px;
            border: none;
            cursor: pointer;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            margin: 10px;
            position: fixed;
            bottom: 10px;
            left: 10px;
        }

        body {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh; /* Assure la pleine hauteur de la fenêtre */
            margin: 0; /* Supprime la marge par défaut du corps */
        }

        h1 {
            text-align: center;
        }

        /* Formulaire pour envoyer un message */
        form {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 80%;
            max-width: 300px; /* Largeur maximale du formulaire */
            margin: 10px auto; /* Centre horizontalement */
        }

        .sms {
            width: 100%; /* 100% de la largeur du formulaire */
            padding: 10px;
            font-size: 16px;
            border: 2px solid #dbdbdb;
            border-radius: 30px;
            background-color: #000000;
            color: white;
            margin: 10px 0; /* Espace en haut et en bas */
            box-sizing: border-box; /* Inclut les bordures dans la largeur */
        }

        input[type="submit"] {
            background-color: #0e0efc;
            background-image: linear-gradient(to right, #0059ff, #006aff);
            border: none;
            color: #ffffff;
            cursor: pointer;
            border-radius: 4px;
            transition: background-color 0.3s ease;
            font-weight: bold;
            width: 100%; /* 100% de la largeur du formulaire */
            padding: 10px;
            font-size: 16px;
            margin: 10px 0; /* Espace en haut et en bas */
        }

        input[type="submit"]:hover {
            background-color: #1e5fa9;
            background-image: linear-gradient(to right, #006aff, #035bff);
        }

        .message-list {
            max-height: 400px; /* Hauteur maximale de la liste des messages */
            overflow-y: auto; /* Activer la barre de défilement verticale en cas de dépassement de la hauteur maximale */
        }

        /* CSS pour aligner la croix de suppression à droite des messages */
        .message {
            display: flex;
            justify-content: space-between; /* Aligner les éléments en espace entre (à droite) */
            align-items: center; /* Aligner verticalement au centre */
        }

        .delete-message {
            cursor: pointer;
        }

        /* Media Queries pour la responsivité */

        /* Pour les écrans plus petits que 600px de large */
        @media (max-width: 600px) {
            h2 {
                position: static; /* Rétablir la position par défaut */
            }
        }
    </style>
</head>
<body class="light-theme">

<!-- Bascule de thème -->
<button id="theme-toggle">Changer de thème</button>
<a href="modifier.php" class="btn btn-primary">Modifier votre profil</a>
<a href="logout.php" class="btn btn-danger">Logout</a>
<a href="chat" class="btn btn-primary">chat.php</a>


<h1>Chat</h1>

<!-- Formulaire pour envoyer un message -->
<form method="POST" action="home.php">
    <input class="sms" name="message" placeholder="Entrez votre message ici" required>
    <input type="submit" value="Envoyer" class="btn btn-success">
</form>

<!-- Liste des messages du chat -->
<div class="message-list">
    <ul>
        <?php
        // Récupérer tous les messages depuis la base de données
        $sql = "SELECT * FROM messages ORDER BY date_creation DESC";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                // Séparer le contenu du message et le nom d'utilisateur
                $message_parts = explode(': ', $row['contenu']);
                $utilisateur = $message_parts[0];
                $contenu_message = $message_parts[1];

                // Créer un lien vers le profil de l'utilisateur
                $lien_profil = '<a href="profil.php?id=' . $row['utilisateur_id'] . '">' . $utilisateur . '</a>';

                // Afficher le message dans un div avec une classe "message"
                echo '<div class="message">';
                echo '<p>' . $lien_profil . ': ' . htmlspecialchars($contenu_message) . '</p>';

                // Afficher la croix de suppression uniquement pour les utilisateurs autorisés
                if ($nom_utilisateur === 'admin' || $nom_utilisateur === 'Noctalivip') {
                    echo '<span class="delete-message" onclick="supprimerMessage(' . $row['id'] . ')">&#10006;</span>';
                }

                // Afficher le lien "Modifier le profil" seulement si l'utilisateur connecté est l'auteur du message
                if ($_SESSION['id_utilisateur'] === $row['utilisateur_id']) {
                    echo '<a href="modifier.php">Modifier le profil</a>';
                }

                echo '</div>'; // Fermer le div du message
            }
        } else {
            echo "Aucun message n'a été trouvé.";
        }
        ?>
    </ul>
</div>

<!-- JavaScript pour supprimer un message -->
<script>
    function supprimerMessage(messageId) {
        // Envoyer une requête AJAX pour supprimer le message
        const xhr = new XMLHttpRequest();
        xhr.onreadystatechange = function () {
            if (xhr.readyState === 4 && xhr.status === 200) {
                // Actualiser la page après la suppression réussie
                location.reload();
            }
        };
        xhr.open('POST', 'home.php', true);
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send('action=supprimer&message_id=' + messageId);
    }
</script>

</body>
</html>
