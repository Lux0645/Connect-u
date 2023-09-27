<?php
// Inclure le fichier de configuration
require_once 'config.php';

// Récupérer tous les messages depuis la base de données
$sql = "SELECT * FROM messages ORDER BY date_creation DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Séparer le contenu du message et le nom d'utilisateur
        $message_parts = explode(': ', $row['contenu']);
        $utilisateur = $message_parts[0];
        $contenu_message = $message_parts[1];

        echo "<li><p>" . htmlspecialchars($contenu_message) . "</p></li>";
    }
} else {
    echo "Aucun message n'a été trouvé.";
}
?>
