<?php
session_start();
include("../database.php");

if (!$conn) {
    die("Erreur de connexion : " . mysqli_connect_error());
}

$message = "";

// Gestion centralisée des erreurs SQL
function handleSQLError($conn, $query) {
    $error_message = "Erreur SQL : " . mysqli_error($conn) . "\nRequête : " . $query;
    error_log($error_message, 3, "sql_errors.log");
    return $error_message;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['id_emprunt'])) {
        $id_emprunt = mysqli_real_escape_string($conn, $_POST['id_emprunt']);

        // Vérifier l'emprunt
        $query = "SELECT e.id_emprunt, e.id, e.CNI, u.emprunts_en_cours, u.type_abonnement 
                  FROM emprunt e 
                  JOIN utilisateur u ON e.CNI = u.CNI 
                  WHERE e.id_emprunt = ?";
        $stmt = mysqli_prepare($conn, $query);
        if (!$stmt) {
            die(handleSQLError($conn, $query));
        }
        mysqli_stmt_bind_param($stmt, "s", $id_emprunt);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        $emprunt = mysqli_fetch_assoc($result);

        if ($emprunt) {
            // Afficher le formulaire de retour
            $form_visible = true;
        } else {
            $message = "Aucun emprunt trouvé avec cet ID.";
        }
    }

    if (isset($_POST['confirm_return'])) {
        $id_emprunt = mysqli_real_escape_string($conn, $_POST['id_emprunt']);
        $CNI = mysqli_real_escape_string($conn, $_POST['CNI']);
        $id_exemplaire = mysqli_real_escape_string($conn, $_POST['id_exemplaire']);
        $emprunts_en_cours = intval($_POST['emprunts_en_cours']);
        $type_abonnement = mysqli_real_escape_string($conn, $_POST['type_abonnement']);
        $etat = mysqli_real_escape_string($conn, $_POST['etat']);

        mysqli_begin_transaction($conn);
        try {
            // Mettre à jour l'exemplaire
            $query_exemplaire = "UPDATE exemplaire SET statut = 'en rayon', etat = ? WHERE id = ?";
            $stmt_exemplaire = mysqli_prepare($conn, $query_exemplaire);
            if (!$stmt_exemplaire) {
                throw new Exception(handleSQLError($conn, $query_exemplaire));
            }
            mysqli_stmt_bind_param($stmt_exemplaire, "ss", $etat, $id_exemplaire);
            mysqli_stmt_execute($stmt_exemplaire);

            // Mise à jour de l'utilisateur
            $nouveaux_emprunts = max(0, $emprunts_en_cours - 1);
            $peut_emprunter = ($type_abonnement === 'occasionnel' && $nouveaux_emprunts < 1) ||
                              ($type_abonnement === 'abonné' && $nouveaux_emprunts < 4) ||
                              ($type_abonnement === 'abonné privilégié' && $nouveaux_emprunts < 8);

            $query_utilisateur = "UPDATE utilisateur SET emprunts_en_cours = ?, peut_emprunter = ? WHERE CNI = ?";
            $stmt_utilisateur = mysqli_prepare($conn, $query_utilisateur);
            if (!$stmt_utilisateur) {
                throw new Exception(handleSQLError($conn, $query_utilisateur));
            }
            mysqli_stmt_bind_param($stmt_utilisateur, "iis", $nouveaux_emprunts, $peut_emprunter, $CNI);
            mysqli_stmt_execute($stmt_utilisateur);

            // Supprimer l'emprunt
            $query_delete = "DELETE FROM emprunt WHERE id_emprunt = ?";
            $stmt_delete = mysqli_prepare($conn, $query_delete);
            if (!$stmt_delete) {
                throw new Exception(handleSQLError($conn, $query_delete));
            }
            mysqli_stmt_bind_param($stmt_delete, "s", $id_emprunt);
            mysqli_stmt_execute($stmt_delete);

            mysqli_commit($conn);
            $message = "Retour de l'emprunt effectué avec succès.";
        } catch (Exception $e) {
            mysqli_rollback($conn);
            $message = "Erreur : " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Retour d'Emprunt</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right,rgb(113, 222, 202), #9face6);
            
            margin: 0;
            padding: 20px;
        }
        h1 {
            color: #333;
        }
        .message {
            margin-bottom: 20px;
            padding: 10px;
            border-radius: 5px;
        }
        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        form {
            background: #fff;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }
        input[type="text"], select {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        button {
            background: #007bff;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            transition: background 0.3s;
        }
        button:hover {
            background: #0056b3;
        }
        .link-back {
            display: inline-block;
            margin-top: 20px;
            color: #007bff;
            text-decoration: none;
        }
        .link-back:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h1>Retour d'Emprunt</h1>

    <?php if (!empty($message)): ?>
        <div class="message <?php echo strpos($message, 'succès') !== false ? 'success' : 'error'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (!isset($form_visible) || !$form_visible): ?>
        <form method="POST">
            <label for="id_emprunt">ID de l'emprunt :</label>
            <input type="text" id="id_emprunt" name="id_emprunt" required>
            <button type="submit">Vérifier</button>
        </form>
    <?php else: ?>
        <form method="POST">
            <input type="hidden" name="id_emprunt" value="<?php echo htmlspecialchars($emprunt['id_emprunt']); ?>">
            <input type="hidden" name="CNI" value="<?php echo htmlspecialchars($emprunt['CNI']); ?>">
            <input type="hidden" name="id_exemplaire" value="<?php echo htmlspecialchars($emprunt['id']); ?>">
            <input type="hidden" name="emprunts_en_cours" value="<?php echo htmlspecialchars($emprunt['emprunts_en_cours']); ?>">
            <input type="hidden" name="type_abonnement" value="<?php echo htmlspecialchars($emprunt['type_abonnement']); ?>">
            <label for="etat">État de l'exemplaire :</label>
            <select name="etat" id="etat" required>
                <option value="bon état">Bon état</option>
                <option value="usagé">Usagé</option>
                <option value="endommagé">Endommagé</option>
            </select>
            <button type="submit" name="confirm_return">Confirmer le retour</button>
        </form>
    <?php endif; ?>

    <a href="choix_gestion.php" class="link-back">Retour au menu de gestion</a>
</body>
</html>
