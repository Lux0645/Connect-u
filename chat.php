<?php
session_start();

// Inclure le fichier de configuration
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: index.php");
    exit();
}

// Vérifier si l'ID de l'utilisateur cible est spécifié dans l'URL
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $utilisateur_id_cible = intval($_GET['id']);
} else {
    // Rediriger en cas d'ID d'utilisateur manquant ou invalide
    header("Location: profil.php");
    exit();
}

// Vérifier si l'utilisateur cible existe dans la base de données
$sql = "SELECT username FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $utilisateur_id_cible);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $nomUtilisateurCible = $row['username'];
} else {
    // L'utilisateur cible n'a pas été trouvé
    header("Location: profil.php");
    exit();
}

// Vérifier si le formulaire de message a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer le contenu du message depuis le formulaire
    $contenuMessage = $_POST['contenu_message'];

    // Insérer le message privé dans la table prv_messages
    $sql_insert_message = "INSERT INTO prv_messages (expediteur_id, destinataire_id, contenu, date_creation) VALUES (?, ?, ?, NOW())";
    $stmt_insert_message = $conn->prepare($sql_insert_message);
    $stmt_insert_message->bind_param("iis", $_SESSION['id_utilisateur'], $utilisateur_id_cible, $contenuMessage);

    if ($stmt_insert_message->execute()) {
        // Message envoyé avec succès, rediriger vers la page de chat
        header("Location: chat.php?id=" . $utilisateur_id_cible);
        exit();
    } else {
        // Erreur lors de l'envoi du message
        $erreurMessage = "Une erreur s'est produite lors de l'envoi du message.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat avec <?php echo $nomUtilisateurCible; ?></title>
    <!-- Ajoutez ici les styles CSS ou liens vers des fichiers CSS -->
</head>
<body>
    <h1>Chat avec <?php echo $nomUtilisateurCible; ?></h1>

    <form method="post" action="">
        <textarea name="contenu_message" rows="4" cols="50" placeholder="Saisissez votre message..." required></textarea><br>
        <input type="submit" value="Envoyer">
    </form>

    <?php
    // Afficher des erreurs éventuelles
    if (isset($erreurMessage)) {
        echo '<p style="color: red;">' . $erreurMessage . '</p>';
    }
    ?>

    <!-- Afficher les messages précédents entre l'utilisateur connecté et l'utilisateur cible -->
    <div id="messages">
        <?php
        // Récupérer les messages entre les utilisateurs depuis la table prv_messages
        $sql_messages = "SELECT contenu, expediteur_id FROM prv_messages WHERE (expediteur_id = ? AND destinataire_id = ?) OR (expediteur_id = ? AND destinataire_id = ?) ORDER BY date_creation ASC";
        $stmt_messages = $conn->prepare($sql_messages);
        $stmt_messages->bind_param("iiii", $_SESSION['id_utilisateur'], $utilisateur_id_cible, $utilisateur_id_cible, $_SESSION['id_utilisateur']);
        $stmt_messages->execute();
        $result_messages = $stmt_messages->get_result();

        while ($message_row = $result_messages->fetch_assoc()) {
            $contenuMessage = htmlspecialchars($message_row['contenu']);
            $expediteur_id = $message_row['expediteur_id'];

            // Afficher le message avec le nom de l'expéditeur
            $expediteur_nom = ($expediteur_id === $_SESSION['id_utilisateur']) ? 'Vous' : $nomUtilisateurCible;
            echo "<p><strong>$expediteur_nom:</strong> $contenuMessage</p>";
        }
        ?>
    </div>
</body>
</html>
