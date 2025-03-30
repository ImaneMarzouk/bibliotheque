<?php
include("../../database.php"); // Inclure la connexion à la base de données

// Récupérer le CNI de l'utilisateur à modifier
$CNI = isset($_GET['CNI']) ? $_GET['CNI'] : '';

// Vérifier si l'utilisateur existe dans la base de données
$sql = "SELECT * FROM UTILISATEUR WHERE CNI = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $CNI);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "Utilisateur non trouvé.";
    exit;
}

// Récupérer les données de l'utilisateur
$user = $result->fetch_assoc();

// Initialiser les variables pour les champs du formulaire
$nom = $user['nom'];
$prenom = $user['Prenom'];
$type_abonnement = $user['type_abonnement'];
$emprunts_en_cours = $user['emprunts_en_cours'];
$peut_emprunter = $user['peut_emprunter'] ? "1" : "0";

// Traitement du formulaire lors de la soumission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Récupérer les nouvelles valeurs depuis le formulaire
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $type_abonnement = $_POST['type_abonnement'];

    // Définir les limites des emprunts en fonction du type d'abonnement
    $max_emprunts = 0;
    if ($type_abonnement == 'occasionnel') {
        $max_emprunts = 1;
    } elseif ($type_abonnement == 'abonné') {
        $max_emprunts = 4;
    } elseif ($type_abonnement == 'abonné privilégié') {
        $max_emprunts = 8;
    }

    // Ajuster les emprunts en cours à la limite si nécessaire
    $emprunts_en_cours = min($_POST['emprunts_en_cours'], $max_emprunts);

    // Mettre à jour peut_emprunter automatiquement
    $peut_emprunter = $emprunts_en_cours < $max_emprunts ? 1 : 0;

    // Mettre à jour l'utilisateur dans la base de données
    $updateSql = "UPDATE UTILISATEUR SET nom = ?, prenom = ?, type_abonnement = ?, emprunts_en_cours = ?, peut_emprunter = ? WHERE CNI = ?";
    $stmt = $conn->prepare($updateSql);
    $stmt->bind_param("sssdis", $nom, $prenom, $type_abonnement, $emprunts_en_cours, $peut_emprunter, $CNI);

    if ($stmt->execute()) {
        echo "Utilisateur mis à jour avec succès.";
        header("Location: gerer_utilisateurs.php"); // Rediriger vers la page de gestion des utilisateurs
        exit;
    } else {
        echo "Erreur lors de la mise à jour de l'utilisateur.";
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Modifier Utilisateur</title>
    <link rel="stylesheet" href="https://unpkg.com/lucide@latest/build/lucide.css">
    <style>
        /* Styles de base */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right,rgb(113, 222, 202), #9face6);
            color: #333;
        }

        h1 {
            text-align: center;
            margin: 20px 0;
            color: #007bff;
        }

        /* Formulaire */
        form {
            max-width: 600px;
            background-color: white;
            padding: 20px;
            margin: 20px auto;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        form label {
            font-size: 14px;
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        form input[type="text"], form input[type="number"], form select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 14px;
            box-sizing: border-box;
            transition: border-color 0.3s ease;
        }

        form input[type="text"]:focus, form input[type="number"]:focus, form select:focus {
            border-color: #007bff;
            outline: none;
            box-shadow: 0 0 5px rgba(0, 123, 255, 0.3);
        }

        form input[type="checkbox"] {
            margin-right: 8px;
            transform: scale(1.2);
        }

        form input[type="submit"] {
            width: 100%;
            padding: 12px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        form input[type="submit"]:hover {
            background-color: #0056b3;
        }

        /* Bouton de retour */
        a.button {
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px auto;
            text-decoration: none;
            background-color: #555;
            color: white;
            padding: 10px 15px;
            border-radius: 5px;
            font-size: 14px;
            width: fit-content;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: background-color 0.3s ease;
        }

        a.button:hover {
            background-color: #333;
        }

        a.button i {
            margin-right: 8px;
        }

        /* Icônes */
        i {
            font-size: 18px;
            vertical-align: middle;
        }
    </style>
</head>
<body>
    <h1>Modifier l'utilisateur</h1>
    <form action="modifier_utilisateur.php?CNI=<?php echo htmlspecialchars($CNI); ?>" method="POST">
        <label for="nom">Nom :</label>
        <input type="text" name="nom" id="nom" value="<?php echo htmlspecialchars($nom); ?>" required>

        <label for="prenom">Prénom :</label>
        <input type="text" name="prenom" id="prenom" value="<?php echo htmlspecialchars($prenom); ?>" required>

        <label for="type_abonnement">Type d'abonnement :</label>
        <select name="type_abonnement" id="type_abonnement" required>
            <option value="occasionnel" <?php echo $type_abonnement == 'occasionnel' ? 'selected' : ''; ?>>occasionnel</option>
            <option value="abonné privilégié" <?php echo $type_abonnement == 'abonné privilégié' ? 'selected' : ''; ?>>abonné privilégié</option>
            <option value="abonné" <?php echo $type_abonnement == 'abonné' ? 'selected' : ''; ?>>abonné </option>
        </select>

        <input type="submit" value="Mettre à jour">
    </form>
    <a href="gerer_utilisateurs.php" class="button">
        <i class="lucide lucide-arrow-left-circle"></i> Retour à la gestion des utilisateurs
    </a>
</body>
</html>