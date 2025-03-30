<?php
include("../database.php");

$message = ""; // Variable pour stocker le message

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Filtrer et valider les données
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password']; // Le mot de passe sera haché avant l'insertion
    $autorisation = filter_input(INPUT_POST, 'autorisation', FILTER_SANITIZE_STRING);

    // Vérification des champs
    if (empty($email) || empty($password) || empty($autorisation)) {
        $message = "<div class='message error'>Veuillez remplir tous les champs.</div>";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $message = "<div class='message error'>L'email n'est pas valide.</div>";
    } else {
        // Validation de l'autorisation
        $valid_autorisation = ["directeur", "principal", "stagaire"];
        if (!in_array($autorisation, $valid_autorisation)) {
            $message = "<div class='message error'>Valeur d'autorisation invalide.</div>";
        } else {
            // Hacher le mot de passe pour la sécurité
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            // Préparation de la requête SQL
            $sql = "INSERT INTO user (email, password, autorisation) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("sss", $email, $hashed_password, $autorisation);

                // Exécution de la requête
                if ($stmt->execute()) {
                    $message = "<div class='message success'>Utilisateur ajouté avec succès. Redirection en cours...</div>";
                    // Redirection vers gestion.php après 3 secondes
                    header("Refresh: 3; URL=gestion.php");
                } else {
                    $message = "<div class='message error'>Erreur lors de l'ajout de l'utilisateur : " . htmlspecialchars($stmt->error) . "</div>";
                }

                $stmt->close();
            } else {
                $message = "<div class='message error'>Erreur de préparation de la requête : " . $conn->error . "</div>";
            }
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Utilisateur</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background: linear-gradient(to right,rgb(113, 222, 202), #9face6);
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 400px;
            margin: 50px auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            color: #333;
        }
        form {
            margin-top: 20px;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: white;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 5px;
            text-align: center;
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
    </style>
</head>
<body>
    <div class="container">
        <h2>Ajouter un Utilisateur</h2>

        <!-- Affichage du message -->
        <?php echo $message; ?>

        <!-- Formulaire -->
        <form method="post" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <input type="email" name="email" required placeholder="Email">
            <input type="password" name="password" required placeholder="Mot de passe">
            <select name="autorisation" required>
                <option value="directeur">Directeur</option>
                <option value="principal">Principal</option>
                <option value="stagaire">Stagaire</option>
            </select>
            <input type="submit" value="Ajouter">
        </form>
    </div>
</body>
</html>
