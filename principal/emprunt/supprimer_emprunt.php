<?php
// Inclure la connexion à la base de données
include("../../database.php");

// Vérifier si un ID d'emprunt est fourni
if (isset($_GET['id_emprunt'])) {
    $id_emprunt = $_GET['id_emprunt'];

    // Préparer la requête pour supprimer l'emprunt
    $delete_query = "DELETE FROM emprunt WHERE id_emprunt = ?";
    $stmt = $conn->prepare($delete_query);
    $stmt->bind_param("s", $id_emprunt);

    // Exécuter la requête et vérifier le résultat
    if ($stmt->execute()) {
        // Si la suppression est réussie, rediriger vers la page liste_emprunt.php avec un message de succès
        echo "<script>
                alert('Emprunt supprimé avec succès.');
                window.location.href = 'liste_emprunt.php';
              </script>";
    } else {
        // En cas d'erreur, afficher un message
        echo "<div style='color: red; font-weight: bold;'>Erreur : " . htmlspecialchars($stmt->error) . "</div>";
    }

    // Fermer la déclaration
    $stmt->close();
} else {
    echo "<div style='color: orange; font-weight: bold;'>Aucun emprunt spécifié à supprimer.</div>";
}

// Fermer la connexion à la base de données
$conn->close();
?>
