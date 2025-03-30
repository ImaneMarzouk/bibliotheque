<?php
session_start(); // Démarrer la session pour utiliser les messages de session

include("../../database.php"); // Inclure la connexion à la base de données

// Vérifier si le CNI est passé en paramètre dans l'URL
if (isset($_GET['CNI'])) {
    $CNI = $_GET['CNI'];

    // Préparer la requête pour supprimer l'utilisateur
    $sql = "DELETE FROM UTILISATEUR WHERE CNI = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $CNI);

    // Exécuter la requête
    if ($stmt->execute()) {
        // Si la suppression a réussi, définir un message de succès dans la session
        $_SESSION['message'] = "Utilisateur supprimé avec succès.";
        $_SESSION['message_type'] = "success"; // Type de message : succès
    } else {
        // Si une erreur survient lors de la suppression
        $_SESSION['message'] = "Erreur lors de la suppression de l'utilisateur.";
        $_SESSION['message_type'] = "error"; // Type de message : erreur
    }

    // Rediriger vers la page de gestion des utilisateurs
    header("Location: gerer_utilisateurs.php");
    exit;
} else {
    // Si le CNI n'est pas spécifié
    $_SESSION['message'] = "Aucun utilisateur spécifié à supprimer.";
    $_SESSION['message_type'] = "error"; // Type de message : erreur
    header("Location: gerer_utilisateurs.php");
    exit;
}
?>
