<?php
// Démarrer la session pour récupérer les données sauvegardées
session_start();

// Inclure la connexion à la base de données
include("../../database.php");

// Initialiser les variables
$message = "";
$CNI = $_SESSION['CNI'] ?? '';
$id_exemplaire = $_SESSION['id'] ?? '';
$Id_user = $_SESSION['user_id'] ?? '';
$date_fin = '';

// Fonction pour gérer les erreurs SQL
function handleSQLError($conn, $query) {
    return "Erreur SQL: " . mysqli_error($conn) . "\nRequête: " . $query;
}

// Récupérer les informations de l'utilisateur
$query_user = "SELECT type_abonnement, emprunts_en_cours FROM utilisateur WHERE CNI = ?";
$stmt_user = $conn->prepare($query_user);
$stmt_user->bind_param("s", $CNI);
$stmt_user->execute();
$result_user = $stmt_user->get_result();
$user = $result_user->fetch_assoc();

if (!$user) {
    $message = "Utilisateur non trouvé. Veuillez vérifier le CNI.";
} else {
    $type_abonnement = $user['type_abonnement'];
    $emprunts_en_cours = $user['emprunts_en_cours'];

    // Déterminer la durée de l'emprunt et la limite d'emprunts
    switch ($type_abonnement) {
        case 'occasionnel':
            $duree = 15;
            $limite_emprunts = 1;
            break;
        case 'abonné':
            $duree = 30;
            $limite_emprunts = 4;
            break;
        case 'abonné privilégié':
            $duree = 30;
            $limite_emprunts = 8;
            break;
        default:
            $message = "Type d'abonnement non reconnu.";
            break;
    }

    // Traiter la soumission du formulaire
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $date_debut = $_POST['date_debut'];
        $date_fin = date('Y-m-d', strtotime($date_debut . " + $duree days"));
        $id_emprunt = uniqid('EMP');

        // Démarrer une transaction
        $conn->begin_transaction();

        try {
            // Insérer les informations de l'emprunt
            $query_insert = "INSERT INTO emprunt (id_emprunt, id, CNI, Id_user, date_debut, date_fin, nombre_rappels) 
                             VALUES (?, ?, ?, ?, ?, ?, 0)";
            $stmt_insert = $conn->prepare($query_insert);
            $stmt_insert->bind_param("ssssss", $id_emprunt, $id_exemplaire, $CNI, $Id_user, $date_debut, $date_fin);
            $stmt_insert->execute();

            // Mettre à jour le statut du livre
            $query_update_book = "UPDATE exemplaire SET statut = 'en prêt' WHERE id = ?";
            $stmt_update_book = $conn->prepare($query_update_book);
            $stmt_update_book->bind_param("s", $id_exemplaire);
            $stmt_update_book->execute();

            // Mettre à jour les informations utilisateur
            $new_emprunts_en_cours = $emprunts_en_cours + 1;
            $peut_emprunter = ($new_emprunts_en_cours < $limite_emprunts) ? 1 : 0;

            $query_update_user = "UPDATE utilisateur SET emprunts_en_cours = ?, peut_emprunter = ? WHERE CNI = ?";
            $stmt_update_user = $conn->prepare($query_update_user);
            $stmt_update_user->bind_param("iis", $new_emprunts_en_cours, $peut_emprunter, $CNI);
            $stmt_update_user->execute();

            // Valider la transaction
            $conn->commit();
            $message = "Emprunt enregistré avec succès. ID de l'emprunt : $id_emprunt";
            if ($peut_emprunter == 0) {
                $message .= ". Attention : vous avez atteint votre limite d'emprunts.";
            }

            // Nettoyer les variables de session
            unset($_SESSION['CNI'], $_SESSION['id'], $_SESSION['Id_user']);
        } catch (Exception $e) {
            $conn->rollback();
            $message = "Erreur lors de l'enregistrement de l'emprunt : " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Validation de l'emprunt</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white text-center">
            <h2>Validation de l'emprunt</h2>
        </div>
        <div class="card-body">
            <?php if (!empty($message)): ?>
                <div class="alert <?php echo strpos($message, 'succès') !== false ? 'alert-success' : 'alert-danger'; ?>">
                    <?php echo htmlspecialchars($message); ?>
                    <?php if (strpos($message, 'succès') !== false): ?>
                        <p>Date de fin de l'emprunt : <strong><?php echo htmlspecialchars($date_fin); ?></strong></p>
                        <a href="liste_emprunt.php">Retour a liste des emprunts</a>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <?php if (empty($message) || strpos($message, 'Emprunt enregistré avec succès') === false): ?>
                <form method="POST" class="row g-3">
                    <div class="col-md-6">
                        <label for="CNI" class="form-label">CNI</label>
                        <input type="text" class="form-control" id="CNI" value="<?php echo htmlspecialchars($CNI); ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="id_exemplaire" class="form-label">ID Exemplaire</label>
                        <input type="text" class="form-control" id="id_exemplaire" value="<?php echo htmlspecialchars($id_exemplaire); ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="Id_user" class="form-label">ID USER</label>
                        <input type="text" class="form-control" id="id_user" value="<?php echo htmlspecialchars($Id_user); ?>" disabled>
                    </div>
                    <div class="col-md-6">
                        <label for="date_debut" class="form-label">Date de début</label>
                        <input type="date" name="date_debut" id="date_debut" class="form-control" required>
                    </div>
                    <div class="col-12">
                        <p>Durée de l'emprunt : <strong><?php echo $duree; ?> jours</strong></p>
                    </div>
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-success">Valider l'emprunt</button>
                        <a href="liste_emprunt.php" class="btn btn-secondary">Retour à la liste des emprunts</a>
                    </div>
                </form>
            <?php endif; ?>
        </div>
    </div>
</div>

<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
</body>
</html>
