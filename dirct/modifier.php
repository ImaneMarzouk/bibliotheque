<?php
include("../database.php");

$id_user = filter_input(INPUT_GET, 'id_user', FILTER_SANITIZE_NUMBER_INT);

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $autorisation = filter_input(INPUT_POST, 'autorisation', FILTER_SANITIZE_STRING);

    if (empty($email) || empty($autorisation)) {
        $error = "Veuillez remplir tous les champs obligatoires.";
    } else {
        if (!empty($password)) {
            // Hash the new password
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $sql = "UPDATE user SET email = ?, password = ?, autorisation = ? WHERE id_user = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssi", $email, $hashed_password, $autorisation, $id_user);
        } else {
            $sql = "UPDATE user SET email = ?, autorisation = ? WHERE id_user = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssi", $email, $autorisation, $id_user);
        }

        if ($stmt->execute()) {
            header("Location: gestion.php?message=Utilisateur modifié avec succès");
            exit();
        } else {
            $error = "Erreur lors de la modification de l'utilisateur : " . $stmt->error;
        }
        $stmt->close();
    }
} else {
    $sql = "SELECT email, autorisation FROM user WHERE id_user = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $result = $stmt->get_result();
    $user = $result->fetch_assoc();
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier un Utilisateur</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background: linear-gradient(to right,rgb(113, 222, 202), #9face6);
            color: #333;
            margin: 0;
            padding: 20px;
        }
        h2 {
            text-align: center;
            color: #444;
        }
        form {
            max-width: 400px;
            margin: 30px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
        input, select {
            width: 100%;
            padding: 12px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 16px;
            box-sizing: border-box;
        }
        input[type="submit"] {
            background-color: #007bff;
            color: #fff;
            border: none;
            cursor: pointer;
            font-size: 16px;
            padding: 15px;
            border-radius: 4px;
        }
        input[type="submit"]:hover {
            background-color: #0056b3;
        }
        .error {
            color: red;
            margin-bottom: 15px;
            font-size: 14px;
            text-align: center;
        }
        p a {
            color: #007bff;
            text-decoration: none;
            display: block;
            text-align: center;
            margin-top: 20px;
        }
        p a:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
    <h2>Modifier un Utilisateur</h2>
    <?php if (isset($error)) echo "<p class='error'>$error</p>"; ?>
    <form method="post">
        <input type="email" name="email" required placeholder="Email" value="<?php echo htmlspecialchars($user['email']); ?>">
        <input type="password" name="password" placeholder="Nouveau mot de passe (laisser vide si inchangé)">
        <select name="autorisation" required>
            <option value="directeur" <?php echo $user['autorisation'] == 'directeur' ? 'selected' : ''; ?>>Directeur</option>
            <option value="principal" <?php echo $user['autorisation'] == 'principal' ? 'selected' : ''; ?>>Principal</option>
            <option value="stagaire" <?php echo $user['autorisation'] == 'stagaire' ? 'selected' : ''; ?>>Stagaire</option>
        </select>
        <input type="submit" value="Modifier">
    </form>
    <p><a href="gestion.php">Retour à la gestion des utilisateurs</a></p>
</body>
</html>