<?php
session_start();
include("../database.php");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = "";

function handleSQLError($conn, $query) {
    $error_message = "Erreur SQL: " . mysqli_error($conn) . "\nRequête: " . $query;
    error_log($error_message, 3, "sql_errors.log");
    return $error_message;
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_emprunt'])) {
    $id_emprunt = mysqli_real_escape_string($conn, $_POST['id_emprunt']);

    // Vérifier si l'emprunt existe et récupérer les informations nécessaires
    $query_check_emprunt = "SELECT e.id_emprunt, e.id, e.CNI, u.emprunts_en_cours, u.type_abonnement
                            FROM emprunt e
                            JOIN utilisateur u ON e.CNI = u.CNI
                            WHERE e.id_emprunt = ?";
    
    $stmt_check_emprunt = mysqli_prepare($conn, $query_check_emprunt);
    if ($stmt_check_emprunt === false) {
        die(handleSQLError($conn, $query_check_emprunt));
    }

    mysqli_stmt_bind_param($stmt_check_emprunt, "s", $id_emprunt);
    if (!mysqli_stmt_execute($stmt_check_emprunt)) {
        die(handleSQLError($conn, $query_check_emprunt));
    }

    $result_check_emprunt = mysqli_stmt_get_result($stmt_check_emprunt);
    $emprunt = mysqli_fetch_assoc($result_check_emprunt);

    if ($emprunt) {
        // L'emprunt existe, afficher le formulaire pour l'état de l'exemplaire
        ?>
        <form method="POST" action="">
            <input type="hidden" name="id_emprunt" value="<?php echo htmlspecialchars($id_emprunt); ?>">
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
        <?php
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
    $nouvel_etat = mysqli_real_escape_string($conn, $_POST['etat']);

    mysqli_begin_transaction($conn);

    try {
        // Mettre à jour l'exemplaire
        $query_update_exemplaire = "UPDATE exemplaire SET statut = 'en rayon', etat = ? WHERE id = ?";
        $stmt_update_exemplaire = mysqli_prepare($conn, $query_update_exemplaire);
        if ($stmt_update_exemplaire === false) {
            throw new Exception(handleSQLError($conn, $query_update_exemplaire));
        }
        mysqli_stmt_bind_param($stmt_update_exemplaire, "ss", $nouvel_etat, $id_exemplaire);
        if (!mysqli_stmt_execute($stmt_update_exemplaire)) {
            throw new Exception(handleSQLError($conn, $query_update_exemplaire));
        }

        // Mettre à jour l'utilisateur
        $nouveaux_emprunts_en_cours = max(0, $emprunts_en_cours - 1);
        
        // Déterminer peut_emprunter en fonction du type d'abonnement et du nombre d'emprunts en cours
        $peut_emprunter = 1; // Par défaut, on suppose que l'utilisateur peut emprunter
        switch ($type_abonnement) {
            case 'occasionnel':
                $peut_emprunter = ($nouveaux_emprunts_en_cours < 1) ? 1 : 0;
                break;
            case 'abonné':
                $peut_emprunter = ($nouveaux_emprunts_en_cours < 4) ? 1 : 0;
                break;
            case 'abonné privilégié':
                $peut_emprunter = ($nouveaux_emprunts_en_cours < 8) ? 1 : 0;
                break;
        }

        $query_update_utilisateur = "UPDATE utilisateur SET emprunts_en_cours = ?, peut_emprunter = ? WHERE CNI = ?";
        $stmt_update_utilisateur = mysqli_prepare($conn, $query_update_utilisateur);
        if ($stmt_update_utilisateur === false) {
            throw new Exception(handleSQLError($conn, $query_update_utilisateur));
        }
        mysqli_stmt_bind_param($stmt_update_utilisateur, "iis", $nouveaux_emprunts_en_cours, $peut_emprunter, $CNI);
        if (!mysqli_stmt_execute($stmt_update_utilisateur)) {
            throw new Exception(handleSQLError($conn, $query_update_utilisateur));
        }

        // Supprimer l'enregistrement d'emprunt
        $query_delete_emprunt = "DELETE FROM emprunt WHERE id_emprunt = ?";
        $stmt_delete_emprunt = mysqli_prepare($conn, $query_delete_emprunt);
        if ($stmt_delete_emprunt === false) {
            throw new Exception(handleSQLError($conn, $query_delete_emprunt));
        }
        mysqli_stmt_bind_param($stmt_delete_emprunt, "s", $id_emprunt);
        if (!mysqli_stmt_execute($stmt_delete_emprunt)) {
            throw new Exception(handleSQLError($conn, $query_delete_emprunt));
        }

        mysqli_commit($conn);
        $message = "Retour de l'emprunt effectué avec succès. L'état de l'exemplaire a été mis à jour et le nombre d'emprunts en cours a été décrémenté.";
        
        // Rediriger vers choix_gestion.php après 3 secondes
        header("refresh:3;url=stagaire.php");
    } catch (Exception $e) {
        mysqli_rollback($conn);
        $message = "Erreur lors du retour de l'emprunt : " . $e->getMessage();
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
    
    <?php if (!empty($message)): ?>
        <div class="message <?php echo strpos($message, 'succès') !== false ? 'success' : ''; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>
    
    <?php if (!isset($_POST['id_emprunt']) && !isset($_POST['confirm_return'])): ?>
        <form method="POST">
            <label for="id_emprunt">ID de l'emprunt :</label>
            <input type="text" id="id_emprunt" name="id_emprunt" required>
            <button type="submit">Vérifier l'emprunt</button>
        </form>
    <?php endif; ?>

    <a href="stagaire.php">Retour au menu de gestion</a>
</body>
</html>

<?php
mysqli_close($conn);
?>

