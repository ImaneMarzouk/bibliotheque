<?php
include("../../database.php");

if (isset($_GET['id_emprunt'])) {
    $id_emprunt = mysqli_real_escape_string($conn, $_GET['id_emprunt']); // Sécurisation de l'entrée

    // Récupérer les données actuelles de l'emprunt
    $query = "SELECT * FROM emprunt WHERE id_emprunt = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $id_emprunt);
    $stmt->execute();
    $result = $stmt->get_result();
    $emprunt = $result->fetch_assoc();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // Récupération et validation des nouvelles données du formulaire
        $id = $_POST['id'];
        $CNI = $_POST['CNI'];
        $date_debut = $_POST['date_debut'];
        $date_fin = $_POST['date_fin'];
        $nombre_rappels = $_POST['nombre_rappels'];

        // Mise à jour de l'emprunt avec une requête préparée
        $update_query = "UPDATE emprunt SET id = ?, CNI = ?, date_debut = ?, date_fin = ?, nombre_rappels = ? WHERE id_emprunt = ?";
        $stmt = $conn->prepare($update_query);
        $stmt->bind_param("ssssss", $id, $CNI, $date_debut, $date_fin, $nombre_rappels, $id_emprunt);

        if ($stmt->execute()) {
            // Redirection avec message de succès
            echo "<script>
                    alert('Emprunt modifié avec succès.');
                    window.location.href = 'liste_emprunt.php';
                  </script>";
        } else {
            echo "Erreur lors de la mise à jour : " . $conn->error;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Emprunt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h1 class="text-center text-primary mb-4">Modifier Emprunt</h1>
    <form method="post" action="modifier_emprunt.php?id_emprunt=<?php echo htmlspecialchars($id_emprunt); ?>" class="shadow-lg p-4 bg-light rounded">
        <div class="mb-3">
            <label for="id" class="form-label">ID Exemplaire:</label>
            <input type="text" name="id" class="form-control" value="<?php echo htmlspecialchars($emprunt['id']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="CNI" class="form-label">CNI Utilisateur:</label>
            <input type="text" name="CNI" class="form-control" value="<?php echo htmlspecialchars($emprunt['CNI']); ?>" required>
        </div>
        
        <div class="mb-3">
            <label for="date_debut" class="form-label">Date Début:</label>
            <input type="date" name="date_debut" class="form-control" value="<?php echo htmlspecialchars($emprunt['date_debut']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="date_fin" class="form-label">Date Fin:</label>
            <input type="date" name="date_fin" class="form-control" value="<?php echo htmlspecialchars($emprunt['date_fin']); ?>" required>
        </div>
        <div class="mb-3">
            <label for="nombre_rappels" class="form-label">Nombre de rappels:</label>
            <input type="number" name="nombre_rappels" class="form-control" value="<?php echo htmlspecialchars($emprunt['nombre_rappels']); ?>">
        </div>
        <button type="submit" class="btn btn-primary w-100">Modifier</button>
    </form>
</div>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.min.js"></script>
</body>
</html>
