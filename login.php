<?php
session_start();
include("database.php");

$message = ""; // Initialisation du message d'erreur

// Vérification des données envoyées via le formulaire
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les valeurs de l'email et du mot de passe depuis le formulaire
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];

    // Requête pour récupérer l'utilisateur par email
    $sql = "SELECT * FROM user WHERE email = ?";
    $stmt = mysqli_prepare($conn, $sql);

    if ($stmt) {
        // Liaison des paramètres pour sécuriser la requête
        mysqli_stmt_bind_param($stmt, "s", $email);

        // Exécution de la requête
        mysqli_stmt_execute($stmt);

        // Récupération des résultats
        $result = mysqli_stmt_get_result($stmt);

        // Vérification si un enregistrement existe
        if ($user = mysqli_fetch_assoc($result)) {
            // Vérification du mot de passe
            if (password_verify($password, $user['password'])) {
                // Stockage de l'ID de l'utilisateur dans la session
                $_SESSION['user_id'] = $user['Id_user'];
                $_SESSION['user_type'] = $user['autorisation'];
                // Vérification du rôle de l'utilisateur
                $role = $user['autorisation']; // 'directeur', 'principal', ou 'stagiaire'

                // Rediriger l'utilisateur en fonction de son rôle
                switch ($role) {
                    case 'directeur':
                        header("Location: dirct/admin.php");
                        exit();
                    case 'principal':
                        header("Location: principal/choix_gestion.php");
                        exit();
                    case 'stagaire':
                        header("Location: stagaire/stagaire.php");
                        exit();
                    default:
                        $message = "Erreur : rôle non reconnu.";
                }
            } else {
                // Message d'erreur si le mot de passe est incorrect
                $message = "Email ou mot de passe incorrect.";
            }
        } else {
            // Message d'erreur si l'email n'existe pas
            $message = "Email ou mot de passe incorrect.";
        }

        // Libération des ressources
        mysqli_stmt_close($stmt);
    } else {
        // Message d'erreur si la requête SQL échoue
        $message = "Erreur lors de la préparation de la requête.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque - Connexion</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="login-container">
        <h1>Bibliothèque</h1>
        <h2>Connexion</h2>
        <form method="POST" action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>">
            <div class="input-group">
                <label for="email">E-mail :</label>
                <input type="email" id="email" name="email" required>
            </div>
            <div class="input-group">
                <label for="password">Mot de passe :</label>
                <input type="password" id="password" name="password" required>
            </div>
            <!-- Affichage du message d'erreur -->
            <?php if (!empty($message)): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <button type="submit" class="btn">Se connecter</button>
        </form>
    </div>
</body>
</html>