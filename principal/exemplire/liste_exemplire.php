<?php
include("../../database.php");

// Récupérer la valeur de la recherche si elle est présente
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Préparer la requête SQL avec un filtrage
$sql = "SELECT * FROM EXEMPLAIRE WHERE id LIKE ? OR reference LIKE ? OR date_achat LIKE ? OR statut LIKE ?";
$stmt = $conn->prepare($sql);
$searchTerm = "%" . $search . "%";
$stmt->bind_param("ssss", $searchTerm,$searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Exemplaires</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right,rgb(113, 222, 202), #9face6);
            color: #333;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            background: rgba(255, 255, 255, 0.9);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.3);
        }
        h1 {
            text-align: center;
            font-size: 2.5em;
            color: #444;
            margin-bottom: 20px;
            text-shadow: 2px 2px 8px rgba(0, 0, 0, 0.2);
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
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        form input[type="submit"]:hover {
            background-color: #218838;
        }
        .button {
            display: inline-block;
            padding: 10px 15px;
            color: white;
            background-color:#0056b3;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
            margin-bottom: 20px;
        }
        .button:hover {
            background-color: #0056b3;
        }
        .delete-button {
            background-color: #dc3545;
        }
        .delete-button:hover {
            background-color: #c82333;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
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
        td {
            vertical-align: middle;
        }
        .actions {
            display: flex;
            gap: 10px;
        }
        .actions button {
            padding: 5px 10px;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .actions .edit {
            background-color: #17a2b8;
            color: white;
        }
        .actions .edit:hover {
            background-color: #138496;
        }
        .actions .delete {
            background-color: #dc3545;
            color: white;
        }
        .actions .delete:hover {
            background-color: #c82333;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1><i class="fas fa-book"></i> Gestion des Exemplaires</h1>

        <!-- Formulaire de recherche -->
        <form action="" method="GET">
            <input type="text" name="search" placeholder="Rechercher " value="<?php echo htmlspecialchars($search); ?>">
            <input type="submit" value="Rechercher">
        </form>

        <a href="ajouter_exemplaire.php" class="button"><i class="fas fa-plus-circle"></i> Ajouter un nouvel exemplaire</a>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Référence</th>
                    <th>Date d'Achat</th>
                    <th>Statut</th>
                    <th>État</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['id']); ?></td>
                    <td><?php echo htmlspecialchars($row['reference']); ?></td>
                    <td><?php echo htmlspecialchars($row['date_achat']); ?></td>
                    <td><?php echo htmlspecialchars($row['statut']); ?></td>
                    <td><?php echo htmlspecialchars($row['etat']); ?></td>
                    <td class="actions">
                        <button class="edit" onclick="window.location.href='modifier_exemplaire.php?id=<?php echo $row['id']; ?>'">
                            <i class="fas fa-edit"></i> Modifier
                        </button>
                        <button class="delete" onclick="deleteExemplaire('<?php echo $row['id']; ?>')">
                            <i class="fas fa-trash"></i> Supprimer
                        </button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <script>
        function deleteExemplaire(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet exemplaire ?')) {
                window.location.href = 'supprimer_exemplaire.php?id=' + id;
            }
        }
    </script>
</body>
</html>
