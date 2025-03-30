<?php
session_start();

// Inclure la connexion à la base de données
include("../../database.php");

// Récupérer les informations de la session
$CNI = $_SESSION['CNI'] ?? null;
$Id_user = $_SESSION['user_id'] ?? null; // Utiliser l'ID utilisateur stocké lors de la connexion

// Initialiser la variable message
$message = "";

// Vérifier si les informations nécessaires sont présentes
if (empty($CNI) || empty($Id_user)) {
    $message = "Informations manquantes. Veuillez vous connecter et recommencer le processus d'emprunt.";
} else {
    // Vérifier si l'utilisateur existe dans la table user
    $query_check_user = "SELECT Id_user FROM user WHERE Id_user = ?";
    $stmt = $conn->prepare($query_check_user);
    if ($stmt) {
        $stmt->bind_param("i", $Id_user);
        $stmt->execute();
        $result_check_user = $stmt->get_result();

        // Vérifier si un résultat a été trouvé
        if ($result_check_user->num_rows > 0) {
            // Redirection vers la validation de l'emprunt
            header("Location: valider_emprunt.php");
            exit();
        } else {
            $message = "Erreur : ID utilisateur non trouvé dans la base de données.";
        }
        $stmt->close();
    } else {
        $message = "Erreur lors de la préparation de la requête.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification de l'Utilisateur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h1 class="text-center">Vérification de l'Utilisateur</h1>
        </div>
        <div class="card-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-danger text-center">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info text-center">
                    Vérification de l'utilisateur en cours...
                </div>
                <script>
                    // Redirection automatique après 2 secondes
                    setTimeout(function() {
                        window.location.href = 'valider_emprunt.php';
                    }, 2000);
                </script>
            <?php endif; ?>

            <!-- Lien de retour -->
            <div class="text-center mt-3">
                <a href="check_utilisateur.php" class="btn btn-secondary">Retour</a>
            </div>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>

