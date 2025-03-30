<?php
include("../../database.php");

$query = "SELECT * FROM emprunt";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Emprunts</title>
    <style>
        /* Style général */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right,rgb(113, 222, 202), #9face6);
            color: #333;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 30px;
            background-color: #ffffff;
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.1);
            border-radius: 10px;
        }

        header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        header h1 {
            font-size: 2rem;
            color: #2c3e50;
        }

        .actions {
            display: flex;
            gap: 20px;
        }

        .btn {
            text-decoration: none;
            padding: 12px 24px;
            font-size: 16px;
            background-color: #3498db;
            color: #fff;
            border-radius: 5px;
            display: inline-block;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
        }

        .btn:hover {
            background-color: #2980b9;
            transform: translateY(-2px);
        }

        .search-btn {
            background-color: #1abc9c;
        }

        .search-btn:hover {
            background-color: #16a085;
        }

        /* Table */
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            font-size: 16px;
            text-align: left;
        }

        .styled-table thead {
            background-color: #34495e;
            color: #ffffff;
        }

        .styled-table th,
        .styled-table td {
            padding: 15px;
            border: 1px solid #ddd;
        }

        .styled-table tbody tr {
            background-color: #f9f9f9;
            transition: background-color 0.3s ease;
        }

        .styled-table tbody tr:nth-of-type(even) {
            background-color: #ecf0f1;
        }

        .styled-table tbody tr:hover {
            background-color: #f1f1f1;
        }

        .styled-table tbody tr:last-child {
            border-bottom: 2px solid #3498db;
        }

        .no-data {
            text-align: center;
            color: #888;
            font-style: italic;
            padding: 20px 0;
        }

        /* Actions */
        .action-link {
            text-decoration: none;
            font-weight: bold;
            border-radius: 5px;
            padding: 6px 12px;
            display: inline-block;
            margin: 5px;
            transition: background-color 0.3s ease;
        }

        .action-link.edit {
            background-color: #f39c12;
            color: #fff;
        }

        .action-link.edit:hover {
            background-color: #e67e22;
        }

        

        

        /* Responsive design */
        @media (max-width: 768px) {
            .container {
                padding: 20px;
            }

            header h1 {
                font-size: 1.5rem;
            }

            .btn {
                padding: 10px 20px;
                font-size: 14px;
            }

            .styled-table th,
            .styled-table td {
                font-size: 14px;
                padding: 10px;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <header>
            <h1>Gestion des Emprunts</h1>
            <div class="actions">
                <a href="check_exemplaire.php" class="btn">Ajouter un Emprunt</a>
                <a href="recherche_emprunt.php" class="btn search-btn">Rechercher</a>
            </div>
        </header>

        <table class="styled-table">
            <thead>
                <tr>
                    <th>ID Emprunt</th>
                    <th>ID Exemplaire</th>
                    <th>CNI</th>
                    <th>ID user</th>
                    <th>Date Début</th>
                    <th>Date Fin</th>
                    <th>Nombre de rappels</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
            <?php
            if (mysqli_num_rows($result) > 0) {
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<tr>
                            <td>" . $row['id_emprunt'] . "</td>
                            <td>" . $row['id'] . "</td>
                            <td>" . $row['CNI'] . "</td>
                            <td>" . $row['Id_user'] . "</td>
                            <td>" . $row['date_debut'] . "</td>
                            <td>" . $row['date_fin'] . "</td>
                            <td>" . $row['nombre_rappels'] . "</td>
                            <td>
                                <a href='modifier_emprunt.php?id_emprunt=" . $row['id_emprunt'] . "' class='action-link edit'>Modifier</a> | 
                               
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='7' class='no-data'>Aucun emprunt trouvé.</td></tr>";
            }
            ?>
            </tbody>
        </table>
    </div>
</body>
</html>
