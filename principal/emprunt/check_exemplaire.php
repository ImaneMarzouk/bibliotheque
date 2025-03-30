<?php
session_start();
// Inclure la connexion à la base de données
include("../../database.php");

// Initialisation des variables
$message = "";

// Vérifier si le formulaire a été soumis
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = mysqli_real_escape_string($conn, $_POST['id']);

    // Vérifier si l'exemplaire est disponible pour emprunt
    $query = "SELECT * FROM exemplaire 
              WHERE id = ? AND statut = 'en rayon' AND etat NOT IN ('endommagé')";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Vérifier les résultats
    if ($result->num_rows > 0) {
        // L'exemplaire est disponible, passer à la deuxième étape
        $_SESSION['id'] = $id;
        header("Location: check_utilisateur.php?id=$id");
        exit();
    } else {
        // L'exemplaire n'est pas disponible ou ne peut pas être prêté
        $message = "L'exemplaire demandé n'est pas disponible pour emprunt. Veuillez vérifier son statut ou son état.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vérification de l'Exemplaire</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header text-white bg-primary">
            <h1 class="text-center">Vérification de l'Exemplaire</h1>
        </div>
        <div class="card-body">
            <?php if (!empty($message)): ?>
                <div class="alert alert-danger text-center">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            <form action="check_exemplaire.php" method="POST" class="p-3">
                <div class="mb-3">
                    <label for="id" class="form-label">ID de l'exemplaire :</label>
                    <input type="text" name="id" id="id" class="form-control" placeholder="Entrez l'ID de l'exemplaire" required>
                </div>
                <div class="text-center">
                    <button type="submit" class="btn btn-success w-50">Vérifier</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
