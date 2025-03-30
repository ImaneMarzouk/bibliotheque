<?php
include("../../database.php"); // Inclure la connexion à la base de données

// Recherche de documents par référence, titre ou type de document
$search = isset($_GET['search']) ? $_GET['search'] : '';
$type = isset($_GET['type']) ? $_GET['type'] : ''; // Ajout du critère type de document

// Base de la requête
$sql = "SELECT 
            D.reference, 
            D.titre, 
            D.annee_publication, 
            D.editeur, 
            D.nombre_exemplaires,
            L.ISBN, 
            L.auteurs, 
            P.ISSN, 
            P.volume, 
            P.numero
        FROM DOCUMENT D
        LEFT JOIN LIVRE L ON D.reference = L.reference
        LEFT JOIN PERIODIQUE P ON D.reference = P.reference
        WHERE (D.reference LIKE ? 
            OR D.titre LIKE ? 
            OR D.annee_publication LIKE ? 
            OR D.editeur LIKE ? 
            OR D.nombre_exemplaires LIKE ?)";

// Ajout de la condition pour le type de document
if (!empty($type)) {
    if ($type == 'Livre') {
        $sql .= " AND L.reference IS NOT NULL";  // Filtre pour les Livres
    } elseif ($type == 'Périodique') {
        $sql .= " AND P.reference IS NOT NULL";  // Filtre pour les Périodiques
    }
}

// Préparation et exécution de la requête
$stmt = $conn->prepare($sql);
$searchTerm = "%$search%";
$stmt->bind_param("sssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();


?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Documents</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }
        .hover-scale {
            transition: transform 0.3s ease;
        }
        .hover-scale:hover {
            transform: scale(1.05);
        }
        .bg-library {
            background: linear-gradient(to right,rgb(113, 222, 202), #9face6);
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
        }
        .glass-effect {
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(8px);
            border-radius: 10px;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            color: #333;
            min-height: 100vh;
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            font-size: 3em;
            margin: 20px 0;
            color: #ffffff;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.7);
        }
        form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        form input[type="text"] {
            padding: 10px;
            width: 300px;
            margin-right: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        form input[type="submit"] {
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        form input[type="submit"]:hover {
            background-color: #0056b3;
        }
        table {
            width: 100%;
            margin: 20px 0;
            border-collapse: collapse;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        tr:hover {
            background-color: #d6e9ff;
        }
        .button {
            display: inline-block;
            padding: 10px 15px;
            margin: 5px;
            color: white;
            background-color: #28a745;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .button:hover {
            background-color: #218838;
        }
        .delete-button {
            background-color: #dc3545;
        }
        .delete-button:hover {
            background-color: #c82333;
        }
        .icon {
            margin-right: 5px;
        }
    </style>
    <script>
        function openDocumentForm(url) {
            window.open(url, 'DocumentForm', 'width=600,height=400');
        }

        function deleteDocument(reference) {
            if (confirm('Êtes-vous sûr de vouloir supprimer ce document ?')) {
                window.location.href = 'supprimer_document.php?reference=' + reference;
            }
        }
    </script>
</head>
<body class="bg-library">
    <div class="container">
        <div class="glass-effect fade-in" style="padding: 20px;">
            <h1 class="fade-in">Gestion des Documents</h1>

            <form action="" method="GET" class="fade-in">
                <input type="text" name="search" placeholder="Rechercher" value="<?php echo htmlspecialchars($search); ?>">
                <input type="submit" value="Rechercher">
            </form>

            <div style="text-align: center;" class="fade-in">
                <button class="button hover-scale" onclick="openDocumentForm('ajouter_document.php')">
                    <i class="fas fa-plus-circle icon"></i> Ajouter un nouveau document
                </button>
            </div>

            <div class="fade-in" style="overflow-x: auto;">
                <table>
                    <thead>
                        <tr>
                            <th>Référence</th>
                            <th>Titre</th>
                            <th>Année de Publication</th>
                            <th>Éditeur</th>
                            <th>Nombre d'Exemplaires</th>
                            <th>Type de Document</th>
                            <th>Données Spécifiques</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['reference']); ?></td>
                            <td><?php echo htmlspecialchars($row['titre']); ?></td>
                            <td><?php echo htmlspecialchars($row['annee_publication']); ?></td>
                            <td><?php echo htmlspecialchars($row['editeur']); ?></td>
                            <td><?php echo htmlspecialchars($row['nombre_exemplaires']); ?></td>
                            <td>
                                <?php 
                                if (!empty($row['ISBN'])) {
                                    echo "<span style='background-color: #e3f2fd; color: #1565c0; padding: 2px 6px; border-radius: 10px;'>Livre</span>";
                                } elseif (!empty($row['ISSN'])) {
                                    echo "<span style='background-color: #e8f5e9; color: #2e7d32; padding: 2px 6px; border-radius: 10px;'>Périodique</span>";
                                } else {
                                    echo "<span style='background-color: #fafafa; color: #616161; padding: 2px 6px; border-radius: 10px;'>Inconnu</span>";
                                }
                                ?>
                            </td>
                            <td>
                                <?php 
                                if (!empty($row['ISBN'])) {
                                    echo "ISBN : " . htmlspecialchars($row['ISBN']) . "<br>Auteurs : " . htmlspecialchars($row['auteurs']);
                                } elseif (!empty($row['ISSN'])) {
                                    echo "ISSN : " . htmlspecialchars($row['ISSN']) . "<br>Volume : " . htmlspecialchars($row['volume']) . "<br>Numéro : " . htmlspecialchars($row['numero']);
                                } else {
                                    echo "N/A";
                                }
                                ?>
                            </td>
                            <td>
                                <button class="button hover-scale" onclick="openDocumentForm('modifier_document.php?reference=<?php echo $row['reference']; ?>')">
                                    <i class="fas fa-edit icon"></i> Modifier
                                </button>
                                <button class="button delete-button hover-scale" onclick="deleteDocument('<?php echo $row['reference']; ?>')">
                                    <i class="fas fa-trash icon"></i> Supprimer
                                </button>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</body>
</html>
