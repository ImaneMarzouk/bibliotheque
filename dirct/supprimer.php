<?php
include("../database.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Suppression d'utilisateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 50px;
            background-color: #f9f9f9;
            color: #333;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            color: #4CAF50;
        }
        .message {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
            font-size: 16px;
        }
        .success {
            background-color: #dff0d8;
            color: #3c763d;
            border: 1px solid #d6e9c6;
        }
        .error {
            background-color: #f2dede;
            color: #a94442;
            border: 1px solid #ebccd1;
        }
        .warning {
            background-color: #fcf8e3;
            color: #8a6d3b;
            border: 1px solid #faebcc;
        }
        a {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #4CAF50;
            text-decoration: none;
        }
        a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Gestion des utilisateurs</h1>

        <?php
        if (isset($_GET['id_user'])) {
            $id_user = intval($_GET['id_user']);

            try {
                $sql = "DELETE FROM user WHERE id_user = ?";
                $stmt = $conn->prepare($sql);

                if ($stmt === false) {
                    throw new Exception("Erreur de préparation de la requête : " . $conn->error);
                }

                $stmt->bind_param("i", $id_user);
                if ($stmt->execute()) {
                    echo "<div class='message success'>Utilisateur supprimé avec succès.</div>";
                } else {
                    throw new Exception("Erreur lors de l'exécution de la requête : " . $stmt->error);
                }

                $stmt->close();
            } catch (Exception $e) {
                echo "<div class='message error'>Une erreur s'est produite : " . htmlspecialchars($e->getMessage()) . "</div>";
            }
        } else {
            echo "<div class='message warning'>ID utilisateur non spécifié.</div>";
        }

        $conn->close();
        ?>

        <a href="gestion.php">Retour à la liste des utilisateurs</a>
    </div>
</body>
</html>
