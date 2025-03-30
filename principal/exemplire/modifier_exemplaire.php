<?php
include("../../database.php"); // Connexion à la base de données

$success = ""; // Variable pour stocker le message de succès
$error = "";   // Variable pour les messages d'erreur

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    $reference = trim($_POST['reference']);
    $date_achat = $_POST['date_achat'];
    $statut = $_POST['statut'];
    $etat = $_POST['etat'];

    // Validation des entrées
    if (!empty($id) && !empty($reference) && !empty($date_achat) && !empty($statut) && !empty($etat)) {
        $sql = "UPDATE EXEMPLAIRE SET reference = ?, date_achat = ?, statut = ?, etat = ? WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssss", $reference, $date_achat, $statut, $etat, $id);

        if ($stmt->execute()) {
            $success = "Modification effectuée avec succès !";
        } else {
            $error = "Une erreur est survenue lors de la modification.";
        }
    } else {
        $error = "Tous les champs doivent être remplis.";
    }
} elseif (isset($_GET['id'])) {
    $id = $_GET['id'];
    $sql = "SELECT * FROM EXEMPLAIRE WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();

    if (!$row) {
        $error = "Aucun exemplaire trouvé avec cet ID.";
    }
} else {
    $error = "ID non spécifié.";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un exemplaire</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #74ebd5, #9face6);
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            color: #333;
        }
        .form-container {
            background: #fff;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            width: 100%;
            max-width: 500px;
        }
        h1 {
            text-align: center;
            font-size: 2em;
            margin-bottom: 20px;
            color: #333;
        }
        .error, .success {
            font-size: 0.9em;
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
        }
        .error {
            color: #e74c3c;
            background-color: #fdecea;
            border: 1px solid #e74c3c;
        }
        .success {
            color: #28a745;
            background-color: #e8f8e9;
            border: 1px solid #28a745;
        }
        form label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        form input, form select, form button {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }
        form button {
            background: #3498db;
            color: white;
            border: none;
            cursor: pointer;
            transition: background 0.3s ease;
        }
        form button:hover {
            background: #2980b9;
        }
        .back-link {
            text-align: center;
            margin-top: 20px;
        }
        .back-link a {
            color: #3498db;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1>Modifier un exemplaire</h1>

        <!-- Affichage des messages de succès ou d'erreur -->
        <?php if (!empty($success)): ?>
            <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php elseif (!empty($error)): ?>
            <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <!-- Affichage du formulaire si les données existent -->
        <?php if (isset($row)): ?>
        <form action="modifier_exemplaire.php" method="POST">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($row['id']); ?>">

            <label for="reference">Référence :</label>
            <input type="text" name="reference" value="<?php echo htmlspecialchars($row['reference']); ?>" required>

            <label for="date_achat">Date d'achat :</label>
            <input type="date" name="date_achat" value="<?php echo htmlspecialchars($row['date_achat']); ?>" required>

            <label for="statut">Statut :</label>
            <select name="statut" required>
                <option value="en rayon" <?php echo ($row['statut'] == 'en rayon') ? 'selected' : ''; ?>>En rayon</option>
                
                <option value="en réserve" <?php echo ($row['statut'] == 'en réserve') ? 'selected' : ''; ?>>En réserve</option>
                <option value="en travaux" <?php echo ($row['statut'] == 'en travaux') ? 'selected' : ''; ?>>En travaux</option>
            </select>

            <label for="etat">État :</label>
            <select name="etat" required>
                <option value="neuf" <?php echo ($row['etat'] == 'neuf') ? 'selected' : ''; ?>>Neuf</option>
                <option value="très bon état" <?php echo ($row['etat'] == 'très bon état') ? 'selected' : ''; ?>>Très bon état</option>
                <option value="bon état" <?php echo ($row['etat'] == 'bon état') ? 'selected' : ''; ?>>Bon état</option>
                <option value="usagé" <?php echo ($row['etat'] == 'usagé') ? 'selected' : ''; ?>>Usagé</option>
                <option value="endommagé" <?php echo ($row['etat'] == 'endommagé') ? 'selected' : ''; ?>>Endommagé</option>
            </select>

            <button type="submit">Modifier l'exemplaire</button>
        </form>
        <?php endif; ?>

        <!-- Lien pour revenir à la gestion des exemplaires -->
        <div class="back-link">
            <a href="liste_exemplire.php">Retour à la gestion des exemplaires</a>
        </div>
    </div>
</body>
</html>
