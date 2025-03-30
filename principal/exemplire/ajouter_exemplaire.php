<?php
include("../../database.php"); // Inclure la connexion à la base de données

$success = ""; // Variable pour stocker le message de succès
$error = ""; // Variable pour les messages d'erreur

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = trim($_POST['id']);
    $reference = trim($_POST['reference']);
    $date_achat = $_POST['date_achat'];
    $statut = $_POST['statut'];
    $etat = $_POST['etat'];

    // Vérification des champs requis
    if (!empty($id) && !empty($reference) && !empty($date_achat) && !empty($statut) && !empty($etat)) {
        // Vérifier si le nombre d'exemplaires existants est inférieur au maximum autorisé
        $sql_check = "SELECT COUNT(*) AS total_exemplaires, nombre_exemplaires 
                      FROM EXEMPLAIRE e 
                      INNER JOIN DOCUMENT d ON e.reference = d.reference 
                      WHERE d.reference = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("s", $reference);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row = $result_check->fetch_assoc();

        if ($row) {
            $total_exemplaires = $row['total_exemplaires'];
            $nombre_exemplaires = $row['nombre_exemplaires'];

            if ($total_exemplaires < $nombre_exemplaires) {
                // Préparer la requête pour éviter les injections SQL
                $sql_insert = "INSERT INTO EXEMPLAIRE (id, reference, date_achat, statut, etat) VALUES (?, ?, ?, ?, ?)";
                $stmt_insert = $conn->prepare($sql_insert);
                $stmt_insert->bind_param("sssss", $id, $reference, $date_achat, $statut, $etat);

                if ($stmt_insert->execute()) {
                    $success = "L'exemplaire a été ajouté avec succès !";
                } else {
                    $error = "Une erreur s'est produite lors de l'ajout.";
                }
            } else {
                $error = "Le nombre maximum d'exemplaires pour cette référence a déjà été atteint.";
            }
        } else {
            $error = "Référence non valide ou introuvable.";
        }
    } else {
        $error = "Veuillez remplir tous les champs obligatoires.";
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un exemplaire</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right, #74ebd5, #9face6);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.8);
            padding: 20px 30px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
            max-width: 500px;
            width: 100%;
        }
        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
            font-size: 1.8em;
        }
        form label {
            display: block;
            margin: 10px 0 5px;
            font-weight: bold;
        }
        form input, form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        form input[type="submit"] {
            background: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            transition: background 0.3s;
        }
        form input[type="submit"]:hover {
            background: #0056b3;
        }
        .error, .success {
            font-size: 0.9em;
            text-align: center;
            margin-bottom: 15px;
            padding: 10px;
            border-radius: 5px;
        }
        .error {
            color: #dc3545;
            background-color: #fdecea;
            border: 1px solid #dc3545;
        }
        .success {
            color: #28a745;
            background-color: #e8f8e9;
            border: 1px solid #28a745;
        }
        a {
            display: block;
            margin-top: 10px;
            text-align: center;
            text-decoration: none;
            color: #007bff;
            font-weight: bold;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h1><i class="fas fa-plus-circle"></i> Ajouter un exemplaire</h1>
        
        <!-- Affichage du message de succès ou d'erreur -->
        <?php if (!empty($success)): ?>
        <div class="success"><?php echo htmlspecialchars($success); ?></div>
        <?php elseif (!empty($error)): ?>
        <div class="error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>

        <form action="ajouter_exemplaire.php" method="POST">
            <label for="id">ID :</label>
            <input type="text" name="id" required>

            <label for="reference">Référence :</label>
            <input type="text" name="reference" required>

            <label for="date_achat">Date d'achat :</label>
            <input type="date" name="date_achat" required>

            <label for="statut">Statut :</label>
            <select name="statut" required>
                <option value="en rayon">En rayon</option>
                
                <option value="en réserve">En réserve</option>
                <option value="en travaux">En travaux</option>
            </select>

            <label for="etat">État :</label>
            <select name="etat" required>
                <option value="neuf">Neuf</option>
                <option value="très bon état">Très bon état</option>
                <option value="bon état">Bon état</option>
                <option value="usagé">Usagé</option>
                <option value="endommagé">Endommagé</option>
            </select>

            <input type="submit" value="Ajouter">
        </form>
        <a href="liste_exemplire.php">Retour à la liste des exemplaires</a>
    </div>
</body>
</html>
