<?php
include("../../database.php");

$message = ""; // Variable pour afficher le message de succès ou d'erreur

// Vérification si l'ID est passé en paramètre
if (isset($_GET['id'])) {
    // Récupérer l'ID de l'exemplaire
    $id = $_GET['id'];

    // Vérifier le statut de l'exemplaire
    $sql_check = "SELECT statut FROM EXEMPLAIRE WHERE id = ?";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param("s", $id);
    $stmt_check->execute();
    $result = $stmt_check->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $statut = $row['statut'];

        // Vérifier si le statut est "en prêt" ou "en réserve"
        if ($statut === "en prêt" || $statut === "en réserve") {
            $message = "Impossible de supprimer cet exemplaire car il est actuellement en '$statut'.";
        } else {
            // Préparer la requête SQL pour supprimer l'exemplaire
            $sql_delete = "DELETE FROM EXEMPLAIRE WHERE id = ?";
            $stmt_delete = $conn->prepare($sql_delete);
            $stmt_delete->bind_param("s", $id);

            // Exécuter la requête
            if ($stmt_delete->execute()) {
                $message = "Exemplaire supprimé avec succès.";
            } else {
                $message = "Erreur lors de la suppression de l'exemplaire.";
            }
        }
    } else {
        $message = "Aucun exemplaire trouvé avec cet ID.";
    }
} else {
    $message = "Aucun ID spécifié pour la suppression.";
}

?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Supprimer un exemplaire</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
        }
        .container {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            text-align: center;
            max-width: 400px;
            width: 90%;
        }
        .container h1 {
            font-size: 1.5em;
            color: #3498db;
            margin-bottom: 20px;
        }
        .message {
            padding: 10px;
            border-radius: 5px;
            font-size: 1em;
            margin-bottom: 20px;
        }
        .message.success {
            background-color: #e8f8e9;
            color: #27ae60;
            border: 1px solid #27ae60;
        }
        .message.error {
            background-color: #fdecea;
            color: #e74c3c;
            border: 1px solid #e74c3c;
        }
        a {
            text-decoration: none;
            color: #3498db;
            font-weight: bold;
            padding: 10px 20px;
            border: 1px solid #3498db;
            border-radius: 5px;
            transition: 0.3s;
        }
        a:hover {
            background-color: #3498db;
            color: white;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestion des Exemplaires</h1>
        <div class="message <?php echo $message === "Exemplaire supprimé avec succès." ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
        <a href="liste_exemplire.php">Retour à la liste des exemplaires</a>
    </div>
</body>
</html>

