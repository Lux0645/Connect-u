<?php
$servername = "localhost";
$username = "u739730891_connect";
$password = "Salut2022!";
$database = "u739730891_connect";

// Connexion à la base de données
$conn = new mysqli($servername, $username, $password, $database);

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion à la base de données a échoué : " . $conn->connect_error);
}
?>
<!---

██╗░░░░░██╗░░░██╗██╗░░██╗
██║░░░░░██║░░░██║╚██╗██╔╝
██║░░░░░██║░░░██║░╚███╔╝░
██║░░░░░██║░░░██║░██╔██╗░
███████╗╚██████╔╝██╔╝╚██╗
╚══════╝░╚═════╝░╚═╝░░╚═╝
--->