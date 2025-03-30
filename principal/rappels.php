<?php
session_start();
include("../database.php");

// Vérification de la connexion à la base de données
if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

$message = "";

// Fonction pour gérer les erreurs SQL
function handleSQLError($conn, $query) {
    return "Erreur SQL: " . mysqli_error($conn) . "\nRequête: " . $query;
}

// Si une requête POST est envoyée pour un rappel
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['id_emprunt'])) {
    $id_emprunt = mysqli_real_escape_string($conn, $_POST['id_emprunt']);

    // Vérifier si l'emprunt existe
    $query_check_emprunt = "SELECT e.id_emprunt, e.CNI, u.nom, u.prenom, e.nombre_rappels, e.id
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
        // L'emprunt existe, mettre à jour le nombre de rappels
        $nouveau_nombre_rappels = $emprunt['nombre_rappels'] + 1;

        $query_update_rappels = "UPDATE emprunt SET nombre_rappels = ? WHERE id_emprunt = ?";
        $stmt_update_rappels = mysqli_prepare($conn, $query_update_rappels);
        if ($stmt_update_rappels === false) {
            die(handleSQLError($conn, $query_update_rappels));
        }

        mysqli_stmt_bind_param($stmt_update_rappels, "is", $nouveau_nombre_rappels, $id_emprunt);
        if (!mysqli_stmt_execute($stmt_update_rappels)) {
            die(handleSQLError($conn, $query_update_rappels));
        }
        // Mise à jour du statut de l'exemplaire
        $id_exemplaire = $emprunt['id'];
        $query_update_statut = "UPDATE exemplaire SET statut = 'en retard' WHERE id = ?";
        $stmt_update_statut = mysqli_prepare($conn, $query_update_statut);
        if (!$stmt_update_statut) {
            die(handleSQLError($conn, $query_update_statut));
        }

        mysqli_stmt_bind_param($stmt_update_statut, "s", $id_exemplaire);
        mysqli_stmt_execute($stmt_update_statut);

        // Envoyer le rappel (par exemple, un message affiché ou un email)
        $nom_complet = $emprunt['nom'] . ' ' . $emprunt['prenom'];
        $message = "Rappel envoyé à $nom_complet. Nombre de rappels: $nouveau_nombre_rappels.";

    } else {
        $message = "Aucun emprunt trouvé avec cet ID.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr"> <!-- Vérifiez si cette ligne est bien formatée -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Envoyer un Rappel</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right,rgb(113, 222, 202), #9face6);
            color: #333;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }

        .container {
            background: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            padding: 30px;
            max-width: 400px;
            text-align: center;
        }

        h1 {
            font-size: 1.8em;
            color: #4facfe;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        input[type="text"] {
            padding: 10px;
            font-size: 1em;
            border: 1px solid #ddd;
            border-radius: 5px;
            outline: none;
            transition: 0.3s ease;
        }

        input[type="text"]:focus {
            border-color: #4facfe;
            box-shadow: 0 0 8px rgba(79, 172, 254, 0.5);
        }

        button {
            padding: 10px 15px;
            background: #4facfe;
            color: #fff;
            font-size: 1em;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: 0.3s ease;
        }

        button:hover {
            background: #00f2fe;
        }

        .message {
            margin: 20px 0;
            padding: 10px;
            border-radius: 5px;
            background: #f4f4f4;
            color: #333;
            font-size: 0.9em;
        }

        a {
            display: inline-block;
            margin-top: 10px;
            text-decoration: none;
            color: #4facfe;
            transition: 0.3s ease;
        }

        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Envoyer un Rappel</h1>
        
        <?php if (!empty($message)): ?>
            <div class="message"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <label for="id_emprunt">ID de l'emprunt :</label>
            <input type="text" id="id_emprunt" name="id_emprunt" placeholder="Entrez l'ID" required>
            <button type="submit">Envoyer le Rappel</button>
        </form>

        <a href="choix_gestion.php">Retour au menu de gestion</a>
    </div>
</body>
</html>