<?php
include("../../database.php"); // Inclure la connexion à la base de données

// Vérifier la connexion
if (!$conn) {
    die("Erreur de connexion à la base de données : " . mysqli_connect_error());
}

$message = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $reference = $_POST['reference'];
    $titre = $_POST['titre'];
    $annee_publication = $_POST['annee_publication'];
    $editeur = $_POST['editeur'];
    $nombre_exemplaires = $_POST['nombre_exemplaires'];
    $type_document = $_POST['type_document'];

    $conn->begin_transaction(); // Démarrer une transaction

    try {
        // Insérer dans la table DOCUMENT
        $sql_document = "INSERT INTO DOCUMENT (reference, titre, annee_publication, editeur, nombre_exemplaires) 
                         VALUES (?, ?, ?, ?, ?)";
        $stmt_document = $conn->prepare($sql_document);
        $stmt_document->bind_param("ssssi", $reference, $titre, $annee_publication, $editeur, $nombre_exemplaires);
        $stmt_document->execute();

        if ($type_document === 'livre') {
            $ISBN = $_POST['ISBN'];
            $auteurs = $_POST['auteurs'];
            $sql_livre = "INSERT INTO LIVRE (reference, ISBN, auteurs) VALUES (?, ?, ?)";
            $stmt_livre = $conn->prepare($sql_livre);
            $stmt_livre->bind_param("sss", $reference, $ISBN, $auteurs);
            $stmt_livre->execute();
        } elseif ($type_document === 'periodique') {
            $ISSN = $_POST['ISSN'];
            $volume = $_POST['volume'];
            $numero = $_POST['numero'];
            $sql_periodique = "INSERT INTO PERIODIQUE (reference, ISSN, volume, numero) VALUES (?, ?, ?, ?)";
            $stmt_periodique = $conn->prepare($sql_periodique);
            $stmt_periodique->bind_param("ssii", $reference, $ISSN, $volume, $numero);
            $stmt_periodique->execute();
        }

        $conn->commit(); // Confirmer la transaction
        $message = "Document ajouté avec succès !";
    } catch (Exception $e) {
        $conn->rollback(); // Annuler la transaction en cas d'erreur
        $message = "Erreur lors de l'ajout du document : " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un document</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(to right,rgb(113, 222, 202), #9face6);
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            height:100vh;
            color: #fff;
        }
        .form-container {
            background: #fff;
            color: #333;
            padding: 40px 30px;
            border-radius: 15px;
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3);
            width: 100%;
            max-width: 600px;
            animation: fadeIn 0.5s ease-in-out;
        }
        .form-container h1 {
            text-align: center;
            margin-bottom: 20px;
            font-weight: 600;
            font-size: 24px;
            color:rgb(3, 140, 44);
        }
        .message {
            text-align: center;
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }
        .message.success {
            background: #28a745;
            color: #fff;
        }
        .message.error {
            background: #dc3545;
            color: #fff;
        }
        label {
            font-weight: 600;
            margin-bottom: 5px;
            display: block;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 16px;
            transition: border 0.3s;
        }
        input:focus, select:focus, textarea:focus {
            border-color: #6c63ff;
            outline: none;
        }
        button {
            width: 100%;
            padding: 12px;
            background:rgb(133, 173, 68);
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            color: #fff;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background:rgb(133, 173, 68);
        }
        .additional-fields {
            display: none;
            margin-top: -10px;
        }
        .form-container .field-group {
            margin-bottom: 20px;
        }
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
    <script>
        function toggleFields() {
            const type = document.querySelector('select[name="type_document"]').value;
            document.getElementById('livre-fields').style.display = (type === 'livre') ? 'block' : 'none';
            document.getElementById('periodique-fields').style.display = (type === 'periodique') ? 'block' : 'none';
        }
    </script>
</head>
<body>
    <div class="form-container">
        <h1>Ajouter un document</h1>
        <?php if (!empty($message)): ?>
            <div class="message <?php echo strpos($message, 'succès') !== false ? 'success' : 'error'; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        <form action="" method="POST">
            <div class="field-group">
                <label for="reference">Référence :</label>
                <input type="text" name="reference" required>
            </div>

            <div class="field-group">
                <label for="titre">Titre :</label>
                <input type="text" name="titre" required>
            </div>

            <div class="field-group">
                <label for="annee_publication">Année de publication :</label>
                <input type="number" name="annee_publication" required>
            </div>

            <div class="field-group">
                <label for="editeur">Éditeur :</label>
                <input type="text" name="editeur" required>
            </div>

            <div class="field-group">
                <label for="nombre_exemplaires">Nombre d'exemplaires :</label>
                <input type="number" name="nombre_exemplaires" required>
            </div>

            <div class="field-group">
                <label for="type_document">Type de document :</label>
                <select name="type_document" onchange="toggleFields()" required>
                    <option value="">-- Sélectionner --</option>
                    <option value="livre">Livre</option>
                    <option value="periodique">Périodique</option>
                </select>
            </div>

            <div id="livre-fields" class="additional-fields">
                <div class="field-group">
                    <label for="ISBN">ISBN :</label>
                    <input type="text" name="ISBN">
                </div>
                <div class="field-group">
                    <label for="auteurs">Auteurs :</label>
                    <textarea name="auteurs"></textarea>
                </div>
            </div>

            <div id="periodique-fields" class="additional-fields">
                <div class="field-group">
                    <label for="ISSN">ISSN :</label>
                    <input type="text" name="ISSN">
                </div>
                <div class="field-group">
                    <label for="volume">Volume :</label>
                    <input type="number" name="volume">
                </div>
                <div class="field-group">
                    <label for="numero">Numéro :</label>
                    <input type="number" name="numero">
                </div>
            </div>

            <button type="submit"><i class="fas fa-plus-circle"></i> Ajouter le document</button>
        </form>
    </div>
</body>
</html>
