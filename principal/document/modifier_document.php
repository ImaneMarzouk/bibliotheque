<?php
include("../../database.php"); // Inclure la connexion à la base de données

// Vérifier si la référence est passée dans l'URL
if (!isset($_GET['reference'])) {
    die("Référence du document non spécifiée.");
}

$reference = $_GET['reference'];

// Récupérer les données existantes du document
$sql = "SELECT D.reference, D.titre, D.annee_publication, D.editeur, D.nombre_exemplaires, 
               L.ISBN, L.auteurs, P.ISSN, P.volume, P.numero
        FROM DOCUMENT D
        LEFT JOIN LIVRE L ON D.reference = L.reference
        LEFT JOIN PERIODIQUE P ON D.reference = P.reference
        WHERE D.reference = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $reference);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("Document non trouvé.");
}

$row = $result->fetch_assoc();

$message = "";

// Traitement de la mise à jour du document
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titre = $_POST['titre'];
    $annee_publication = $_POST['annee_publication'];
    $editeur = $_POST['editeur'];
    $nombre_exemplaires = $_POST['nombre_exemplaires'];
    $type_document = $_POST['type_document'];

    // Mise à jour de la table LIVRE si le type est livre
    if ($type_document === 'livre') {
        $ISBN = $_POST['ISBN'];
        $auteurs = $_POST['auteurs'];
        // Mise à jour dans la table LIVRE
        $sql_livre = "UPDATE LIVRE SET ISBN = ?, auteurs = ? WHERE reference = ?";
        $stmt_livre = $conn->prepare($sql_livre);
        $stmt_livre->bind_param("sss", $ISBN, $auteurs, $reference);
        $stmt_livre->execute();
    } 
    // Mise à jour de la table PERIODIQUE si le type est périodique
    elseif ($type_document === 'periodique') {
        $ISSN = $_POST['ISSN'];
        $volume = $_POST['volume'];
        $numero = $_POST['numero'];
        // Mise à jour dans la table PERIODIQUE
        $sql_periodique = "UPDATE PERIODIQUE SET ISSN = ?, volume = ?, numero = ? WHERE reference = ?";
        $stmt_periodique = $conn->prepare($sql_periodique);
        $stmt_periodique->bind_param("ssss", $ISSN, $volume, $numero, $reference);
        $stmt_periodique->execute();
    }

    // Mise à jour dans la table DOCUMENT
    $sql_document = "UPDATE DOCUMENT SET titre = ?, annee_publication = ?, editeur = ?, nombre_exemplaires = ? WHERE reference = ?";
    $stmt_document = $conn->prepare($sql_document);
    $stmt_document->bind_param("sssss", $titre, $annee_publication, $editeur, $nombre_exemplaires, $reference);

    if ($stmt_document->execute()) {
        $message = "Document mis à jour avec succès !";
        // Recharger les données du document après la mise à jour
        $row = array_merge($row, $_POST);
    } else {
        $message = "Erreur lors de la mise à jour du document : " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un document</title>
    <style>
        body { font-family: 'Arial', sans-serif; background-color: #f4f7fc; color: #333; margin: 0; padding: 20px; }
        h1 { text-align: center; color: #007bff; }
        .form-container { max-width: 800px; margin: 20px auto; background-color: #ffffff; padding: 20px; border-radius: 8px; box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1); }
        .message { text-align: center; margin-bottom: 20px; font-weight: bold;color:green }
        form { display: flex; flex-direction: column; gap: 15px; }
        label { font-weight: bold; color: #333; }
        input, textarea, select { padding: 10px; font-size: 16px; border-radius: 5px; border: 1px solid #ccc; }
        input:focus, textarea:focus, select:focus { border-color: #007bff; outline: none; }
        button { background-color: #007bff; color: white; padding: 12px 20px; border: none; border-radius: 5px; cursor: pointer; transition: background-color 0.3s ease; }
        button:hover { background-color: #0056b3; }
        .toggle-section { display: none; }
    </style>
    <script>
        function toggleFields() {
            const type = document.querySelector('select[name="type_document"]').value;
            document.getElementById('livre-fields').style.display = (type === 'livre') ? 'block' : 'none';
            document.getElementById('periodique-fields').style.display = (type === 'periodique') ? 'block' : 'none';
        }

        window.onload = function() {
            toggleFields(); // Call it on page load to ensure the correct sections are shown
        };
    </script>
</head>
<body>
    <h1>Modifier un document</h1>
    <div class="form-container">
        <div class="message"><?php echo $message; ?></div>
        <form action="" method="POST">
            <label for="reference">Référence :</label>
            <input type="text" name="reference" value="<?php echo htmlspecialchars($row['reference']); ?>" readonly>

            <label for="titre">Titre :</label>
            <input type="text" name="titre" value="<?php echo htmlspecialchars($row['titre']); ?>" required>

            <label for="annee_publication">Année de publication :</label>
            <input type="number" name="annee_publication" value="<?php echo htmlspecialchars($row['annee_publication']); ?>" required>

            <label for="editeur">Éditeur :</label>
            <input type="text" name="editeur" value="<?php echo htmlspecialchars($row['editeur']); ?>" required>

            <label for="nombre_exemplaires">Nombre d'exemplaires :</label>
            <input type="number" name="nombre_exemplaires" value="<?php echo htmlspecialchars($row['nombre_exemplaires']); ?>" required>

            <label for="type_document">Type de document :</label>
            <select name="type_document" onchange="toggleFields()" required>
                <option value="">-- Sélectionner --</option>
                <option value="livre" <?php echo ($row['ISBN'] ? 'selected' : ''); ?>>Livre</option>
                <option value="periodique" <?php echo ($row['ISSN'] ? 'selected' : ''); ?>>Périodique</option>
            </select>

            <!-- Champs spécifiques pour Livre -->
            <div id="livre-fields" class="toggle-section" style="display: <?php echo ($row['ISBN'] ? 'block' : 'none'); ?>;">
                <label for="ISBN">ISBN :</label>
                <input type="text" name="ISBN" value="<?php echo htmlspecialchars($row['ISBN']); ?>">

                <label for="auteurs">Auteurs :</label>
                <textarea name="auteurs"><?php echo htmlspecialchars($row['auteurs']); ?></textarea>
            </div>

            <!-- Champs spécifiques pour Périodique -->
            <div id="periodique-fields" class="toggle-section" style="display: <?php echo ($row['ISSN'] ? 'block' : 'none'); ?>;">
                <label for="ISSN">ISSN :</label>
                <input type="text" name="ISSN" value="<?php echo htmlspecialchars($row['ISSN']); ?>">

                <label for="volume">Volume :</label>
                <input type="number" name="volume" value="<?php echo htmlspecialchars($row['volume']); ?>">

                <label for="numero">Numéro :</label>
                <input type="number" name="numero" value="<?php echo htmlspecialchars($row['numero']); ?>">
            </div>

            <button type="submit">Mettre à jour le document</button>

<!-- Lien vers la liste des documents sous forme d'image -->
            <div style="margin-top: 20px; text-align: center;">
                <a href="liste_document.php" class="btn btn-secondary">Retour à la liste des documents</a>
                </a>
            </div>

        </form>
    </div> 
</body>
</html>
