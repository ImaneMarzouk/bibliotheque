<?php
include("../../database.php"); // Inclure la connexion à la base de données

// Vérifier si la référence est passée dans l'URL
if (!isset($_GET['reference'])) {
    die("Référence du document non spécifiée.");
}

$reference = $_GET['reference'];

// Supprimer d'abord les enregistrements dans les tables dépendantes
$sql_exemplaire = "DELETE FROM EXEMPLAIRE WHERE reference = ?";
$stmt_exemplaire = $conn->prepare($sql_exemplaire);
$stmt_exemplaire->bind_param("s", $reference);
$stmt_exemplaire->execute();

// Supprimer ensuite les données dans les tables LIVRE et PERIODIQUE, si elles existent
$sql_livre = "DELETE FROM LIVRE WHERE reference = ?";
$stmt_livre = $conn->prepare($sql_livre);
$stmt_livre->bind_param("s", $reference);
$stmt_livre->execute();

$sql_periodique = "DELETE FROM PERIODIQUE WHERE reference = ?";
$stmt_periodique = $conn->prepare($sql_periodique);
$stmt_periodique->bind_param("s", $reference);
$stmt_periodique->execute();

// Puis supprimer le document de la table DOCUMENT
$sql_document = "DELETE FROM DOCUMENT WHERE reference = ?";
$stmt_document = $conn->prepare($sql_document);
$stmt_document->bind_param("s", $reference);

if ($stmt_document->execute()) {
    $message = "Document supprimé avec succès.";
} else {
    $message = "Erreur lors de la suppression du document : " . $conn->error;
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppression du Document</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color:rgb(207, 201, 201);
        }
        .container {
            text-align: center;
            padding: 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.27);
            width: 50%;
        }
        h1 {
            color: #333;
        }
        .message {
            margin-top: 20px;
            font-size: 18px;
            color:rgb(210, 2, 2);
        }
        .button {
            margin-top: 20px;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            cursor: pointer;
        }
        .button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Suppression du Document</h1>

    <div class="message">
        <?php echo $message; ?>
    </div>
    <br>
    <a href="liste_document.php" class="button">Retour à la liste des documents</a>
</div>

</body>
</html>
