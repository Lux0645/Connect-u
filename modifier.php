<?php
session_start();

// Inclure le fichier de configuration
require_once 'config.php';

// Vérifier si l'utilisateur est connecté
if (!isset($_SESSION['id_utilisateur'])) {
    header("Location: index.php");
    exit();
}

// Récupérer l'ID de l'utilisateur connecté
$utilisateur_id = $_SESSION['id_utilisateur'];

// Fonction pour renommer le fichier en évitant les conflits
function renommerFichier($cheminDossier, $nomFichier) {
    $extension = pathinfo($nomFichier, PATHINFO_EXTENSION);
    $nomUnique = uniqid() . '.' . $extension;
    return $cheminDossier . $nomUnique;
}

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Récupérer les données du formulaire
    $nouveauNomUtilisateur = $_POST['nouveau_nom_utilisateur'];
    $nouvelleBiographie = $_POST['nouvelle_biographie'];

    // Gérer le téléchargement de la nouvelle photo de profil
    $nouvellePhotoProfil = null;
    if ($_FILES['nouvelle_photo_profil']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'profils/';
        $uploadPath = $uploadDir . basename($_FILES['nouvelle_photo_profil']['name']);

        // Renommer le fichier téléchargé s'il existe déjà
        $nouvellePhotoProfil = renommerFichier($uploadDir, $_FILES['nouvelle_photo_profil']['name']);

        if (move_uploaded_file($_FILES['nouvelle_photo_profil']['tmp_name'], $nouvellePhotoProfil)) {
            // Le téléchargement a réussi
        } else {
            // Le téléchargement a échoué
            echo "Erreur lors du téléchargement de la photo de profil.";
            $nouvellePhotoProfil = null;
        }
    }

    // Mettre à jour les informations de l'utilisateur dans la base de données
    $sql = "UPDATE users
            SET username = ?, photo_profil = ?, biographie = ?
            WHERE id = ?";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssi", $nouveauNomUtilisateur, $nouvellePhotoProfil, $nouvelleBiographie, $utilisateur_id);

    if ($stmt->execute()) {
        // Mise à jour réussie, rediriger vers le profil de l'utilisateur
        header('Location: profil.php');
        exit();
    } else {
        // Une erreur s'est produite lors de la mise à jour
        echo "Erreur lors de la mise à jour des informations.";
    }
}

// Récupérer les informations actuelles de l'utilisateur
$sql = "SELECT username, photo_profil, biographie FROM users WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $utilisateur_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $row = $result->fetch_assoc();
    $nomUtilisateurActuel = $row['username'];
    $photoProfilActuelle = $row['photo_profil'];
    $biographieActuelle = $row['biographie'];
} else {
    // L'utilisateur n'a pas été trouvé
    echo "Utilisateur non trouvé.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier le profil</title>
</head>
<body>

<h1>Modifier le profil</h1>

<form method="POST" action="modifier.php" enctype="multipart/form-data">
    <label for="nouveau_nom_utilisateur">Nom d'utilisateur:</label>
    <input type="text" name="nouveau_nom_utilisateur" value="<?php echo $nomUtilisateurActuel; ?>" required>
    <br>

    <label for="nouvelle_photo_profil">Télécharger une nouvelle photo de profil:</label>
    <input type="file" name="nouvelle_photo_profil">
    <br>

    <label for="nouvelle_biographie">Biographie:</label>
    <textarea name="nouvelle_biographie"><?php echo $biographieActuelle; ?></textarea>
    <br>

    <input type="submit" value="Enregistrer les modifications">
</form>

<a href="profil.php">Retour au profil</a>

</body>
</html>
