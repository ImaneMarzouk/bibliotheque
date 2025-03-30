<?php
session_start();
include("../database.php");

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Récupérer les emprunts avec 3 rappels ou plus et les informations des exemplaires associés
$query = "
    SELECT e.id_emprunt, e.id AS id, u.nom, u.prenom, u.CNI, e.date_fin, e.nombre_rappels 
    FROM emprunt e
    JOIN utilisateur u ON e.CNI = u.CNI
    WHERE e.nombre_rappels >= 3
";

$result = mysqli_query($conn, $query);

// Vérification de la requête
if (!$result) {
    die("Erreur SQL: " . mysqli_error($conn));
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Notifications</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f0f9ff, #cbebff);
            color: #333;
        }

        h1 {
            text-align: center;
            margin: 20px 0;
            font-size: 2.5em;
            color: #0056b3;
        }

        p {
            margin: 15px;
            padding: 15px;
            border-radius: 10px;
            background-color: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            font-size: 1.1em;
            line-height: 1.6em;
        }

        p strong {
            color: #ff5722;
        }

        a {
            display: block;
            margin: 30px auto;
            width: 200px;
            text-align: center;
            padding: 10px;
            text-decoration: none;
            color: #fff;
            background-color: #0056b3;
            border-radius: 5px;
            font-size: 1.2em;
            transition: background-color 0.3s ease;
        }

        a:hover {
            background-color: #ff5722;
        }

        .container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
        }

        footer {
            text-align: center;
            margin-top: 20px;
            font-size: 0.9em;
            color: #777;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Notifications des emprunts</h1>

        <?php if ($result && mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <p>
                📢 L'exemplaire <strong><?php echo htmlspecialchars($row['id']); ?></strong>, emprunté par <strong><?php echo htmlspecialchars($row['nom'] . " " . $row['prenom']); ?></strong> sous l'ID emprunt <strong><?php echo htmlspecialchars($row['id_emprunt']); ?></strong>,
                n'a toujours pas été retourné après <strong><?php echo htmlspecialchars($row['nombre_rappels']); ?></strong> rappels. <br> Date de fin de l'emprunt : <strong><?php echo htmlspecialchars($row['date_fin']); ?></strong>.
                </p>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Aucune notification pour le moment.</p>
        <?php endif; ?>

        <a href="admin.php">Retour au menu principal</a>
    </div>
    <footer>
        &copy; <?php echo date("Y"); ?> Gestion des emprunts. Tous droits réservés.
    </footer>
</body>
</html>
