<?php
session_start();

// Inclure la connexion à la base de données
include("../../database.php");

// Initialisation des variables
$message = "";

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['CNI'])) {
    $CNI = mysqli_real_escape_string($conn, $_POST['CNI']);

    // Récupérer les informations de l'utilisateur
    $query_user = "SELECT * FROM utilisateur WHERE CNI = ?";
    $stmt = $conn->prepare($query_user);
    $stmt->bind_param("s", $CNI);
    $stmt->execute();
    $result_user = $stmt->get_result();

    // Vérifier si l'utilisateur existe
    if ($result_user->num_rows > 0) {
        $user = $result_user->fetch_assoc();
        $categorie = $user['type_abonnement']; // Occasionnel, Abonné, Abonné privilégié
        $peut_emprunter = $user['peut_emprunter']; // 1 si l'utilisateur peut emprunter, 0 sinon
        $emprunts_en_cours = $user['emprunts_en_cours']; // Nombre d'emprunts en cours
        
        if ($peut_emprunter == 0) {
            $message = "Vous n'êtes pas autorisé à emprunter des documents pour le moment.";
        } else {
            // Définir les règles selon la catégorie
            $limite_emprunts = 0;
            $duree_emprunt = 0;

            switch ($categorie) {
                case 'occasionnel':
                    $limite_emprunts = 1;
                    $duree_emprunt = 15;
                    break;
                case 'abonné':
                    $limite_emprunts = 4;
                    $duree_emprunt = 30;
                    break;
                case 'abonné privilégié':
                    $limite_emprunts = 8;
                    $duree_emprunt = 30;
                    break;
                default:
                    $message = "Catégorie utilisateur inconnue.";
                    break;
            }

            if ($emprunts_en_cours < $limite_emprunts) {
                // Redirection vers la page suivante
                $_SESSION['CNI'] = $CNI;
                header("Location: check_user.php?CNI=$CNI&duree=$duree_emprunt");
                exit();
            } else {
                $message = "Vous avez atteint la limite d'emprunts autorisés pour votre catégorie.";
            }
        }
    } else {
        $message = "Utilisateur introuvable. Veuillez vérifier le CNI.";
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
        <div class="card-header text-white bg-primary">
            <h1 class="text-center">Vérification de l'Utilisateur</h1>
        </div>
        <div class="card-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-danger text-center">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <form action="check_utilisateur.php" method="POST" class="p-3">
                <div class="mb-3">
                    <label for="CNI" class="form-label">CNI de l'utilisateur :</label>
                    <input type="text" name="CNI" id="CNI" class="form-control" placeholder="Entrez le CNI de l'utilisateur" required>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-success w-50">Suivant</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
