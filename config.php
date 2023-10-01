<?php
$servername = "";
$username = "";
$password = "";
$database = "";

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
