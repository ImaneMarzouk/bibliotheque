<?php
include("../../database.php"); // Inclure la connexion à la base de données

// Vérifier si le formulaire a été soumis
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Récupérer les données du formulaire
    $CNI = $_POST['CNI'];
    $nom = $_POST['nom'];
    $prenom = $_POST['prenom'];
    $type_abonnement = $_POST['type_abonnement'];
    $emprunts_en_cours = (int)$_POST['emprunts_en_cours'];
    $peut_emprunter = isset($_POST['peut_emprunter']) ? 1 : 0;

    // Définir les limites par type d'abonnement
    $limites = [
        "occasionnel" => 1,
        "abonné" => 4,
        "abonné privilégié" => 8
    ];

    // Vérifier si le type d'abonnement est valide
    if (!array_key_exists($type_abonnement, $limites)) {
        die("Type d'abonnement invalide.");
    }

    // Vérifier si la valeur d'emprunts_en_cours respecte la limite
    if ($emprunts_en_cours > $limites[$type_abonnement]) {
        die("Le nombre d'emprunts dépasse la limite autorisée pour ce type d'abonnement.");
    }

    // Insérer les données dans la base de données
    $sql = "INSERT INTO UTILISATEUR (CNI, nom, prenom, type_abonnement, emprunts_en_cours, peut_emprunter) 
            VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssii", $CNI, $nom, $prenom, $type_abonnement, $emprunts_en_cours, $peut_emprunter);

    if ($stmt->execute()) {
        // Rediriger vers la page de gestion des utilisateurs après l'ajout
        header("Location: gerer_utilisateurs.php");
        exit();
    } else {
        echo "Erreur lors de l'ajout de l'utilisateur: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ajouter un Utilisateur</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(to right, rgb(113, 222, 202), #9face6);
            color: #333;
        }

        h1 {
            text-align: center;
            color: #4CAF50;
            margin-bottom: 20px;
        }

        form {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        label {
            font-weight: bold;
            display: block;
            margin-bottom: 5px;
        }

        input[type="text"],
        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-sizing: border-box;
        }

        input[type="checkbox"] {
            margin-right: 5px;
        }

        input[type="submit"] {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }

        input[type="submit"]:hover {
            background-color: #45a049;
        }

        .button {
            display: inline-block;
            padding: 10px 15px;
            margin-top: 10px;
            color: white;
            background-color: #007bff;
            text-decoration: none;
            border-radius: 5px;
            text-align: center;
            font-size: 16px;
        }

        .button:hover {
            background-color: #0056b3;
        }

        .icon {
            margin-right: 8px;
        }

        a {
            display: block;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>
    <h1><i class="fas fa-user-plus icon"></i> Ajouter un Utilisateur</h1>

    <form action="ajouter_utilisateur.php" method="POST">
        <label for="CNI">CNI :</label>
        <input type="text" id="CNI" name="CNI" placeholder="Entrer le CNI" required>

        <label for="nom">Nom :</label>
        <input type="text" id="nom" name="nom" placeholder="Entrer le nom" required>

        <label for="prenom">Prénom :</label>
        <input type="text" id="prenom" name="prenom" placeholder="Entrer le prénom" required>

        <label for="type_abonnement">Type d'abonnement :</label>
        <select id="type_abonnement" name="type_abonnement" required>
            <option value="occasionnel">Occasionnel</option>
            <option value="abonné">Abonné</option>
            <option value="abonné privilégié">Abonné Privilégié</option>
        </select>

        <label for="emprunts_en_cours">Emprunts en cours :</label>
        <input type="number" id="emprunts_en_cours" name="emprunts_en_cours" value="0" required>

        <label for="peut_emprunter">
            <input type="checkbox" id="peut_emprunter" name="peut_emprunter"> Peut emprunter
        </label>

        <input type="submit" value="Ajouter l'Utilisateur">
    </form>

    <a href="gerer_utilisateurs.php" class="button">
        <i class="fas fa-arrow-left icon"></i> Retour à la gestion des utilisateurs
    </a>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const typeAbonnement = document.getElementById("type_abonnement");
            const empruntsEnCours = document.getElementById("emprunts_en_cours");

            // Définir les limites pour chaque type d'abonnement
            const limites = {
                "occasionnel": 1,
                "abonné": 4,
                "abonné privilégié": 8
            };

            // Mettre à jour la limite en fonction du type d'abonnement sélectionné
            typeAbonnement.addEventListener("change", function () {
                const max = limites[typeAbonnement.value];
                empruntsEnCours.max = max; // Fixer la valeur maximale
                empruntsEnCours.value = Math.min(empruntsEnCours.value, max); // Réduire la valeur si nécessaire
            });

            // Initialiser la limite au chargement
            typeAbonnement.dispatchEvent(new Event("change"));
        });
    </script>
</body>
</html>

