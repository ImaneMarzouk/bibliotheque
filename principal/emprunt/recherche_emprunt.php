<?php
include("../../database.php");

// Initialiser la variable $result à null
$result = null;
$searchTerm = ''; // Pour stocker le terme de recherche

if (isset($_POST['search'])) {
    // Récupérer l'attribut choisi et le terme de recherche
    $attribute = $_POST['attribute'];
    $searchTerm = mysqli_real_escape_string($conn, $_POST['searchTerm']);
    
    // Construire la requête en fonction de l'attribut choisi
    $query = "SELECT * FROM emprunt WHERE $attribute LIKE '%$searchTerm%'";

    $result = mysqli_query($conn, $query);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recherche Emprunts</title>
    <style>
        /* Style général */
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to right,rgb(113, 222, 202), #9face6);
            color: #fff;
            line-height: 1.6;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.9);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            border-radius: 15px;
        }

        header {
            margin-bottom: 30px;
            text-align: center;
        }

        header h1 {
            font-size: 2.5rem;
            color: #333;
            text-transform: uppercase;
            letter-spacing: 2px;
        }

        /* Formulaire de recherche */
        .search-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
            margin-bottom: 30px;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
        }

        .search-form label {
            font-size: 1.2rem;
            color: #333;
        }

        .search-form select,
        .search-form input[type="text"] {
            padding: 12px;
            font-size: 1.1rem;
            border-radius: 8px;
            border: 1px solid #ddd;
            transition: all 0.3s ease;
        }

        .search-form select:focus,
        .search-form input[type="text"]:focus {
            border-color:rgb(71, 146, 212);
            box-shadow: 0 0 10px rgba(0, 145, 255, 0.24);
        }

        .search-form input[type="submit"] {
            padding: 12px 25px;
            font-size: 1.1rem;
            background-color:rgb(83, 138, 227);
            color: #fff;
            border-radius: 8px;
            border: none;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-form input[type="submit"]:hover {
            background-color:rgba(9, 116, 238, 0.26);
        }

        /* Table */
        .styled-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 16px;
            text-align: left;
            margin-top: 20px;
        }

        .styled-table thead {
            background-color: #34495e;
            color: #fff;
            font-size: 1.2rem;
        }

        .styled-table th,
        .styled-table td {
            
            padding: 15px;
            border: 1px solid #ddd;
        }

        .styled-table tbody tr {
            background-color:rgba(0, 32, 192, 0);
            transition: background-color 0.3s ease;
            color:#333
        }

        .styled-table tbody tr:nth-of-type(even) {
            background-color:rgb(249, 249, 249);
        }

        

        .styled-table tbody tr:last-child {
            border-bottom: 2px solidrgb(75, 146, 222);
        }

        /* Actions */
        .action-link {
            text-decoration: none;
            font-weight: bold;
            border-radius: 8px;
            padding: 8px 15px;
            display: inline-block;
            margin: 5px;
            transition: all 0.3s ease;
        }

        .action-link.edit {
            background-color: #f39c12;
            color: #fff;
        }

        .action-link.edit:hover {
            background-color: #e67e22;
            transform: translateY(-3px);
        }

        /* Responsive design */
        @media (max-width: 768px) {
            .container {
                padding: 15px;
            }

            header h1 {
                font-size: 1.8rem;
            }

            .search-form select,
            .search-form input[type="text"] {
                padding: 10px;
                font-size: 1rem;
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
            <h1>Recherche des Emprunts</h1>
        </header>

        <!-- Formulaire de recherche -->
        <form method="post" action="recherche_emprunt.php" class="search-form">
            <label for="attribute">Choisir un attribut :</label>
            <select name="attribute" required>
                <option value="id_emprunt">ID Emprunt</option>
                <option value="id">ID Exemplaire</option>
                <option value="CNI">CNI Utilisateur</option>
                <option value="Id_user">ID User</option>
                <option value="date_debut">Date Début</option>
                <option value="date_fin">Date Fin</option>
                <option value="nombre_rappels">Nombre de rappels</option>
            </select><br>

            <label for="searchTerm">Rechercher :</label>
            <input type="text" name="searchTerm" placeholder="Rechercher..." required><br>

            <input type="submit" name="search" value="Rechercher">
        </form>

        <?php
        // Afficher les résultats de recherche uniquement si la recherche a été effectuée
        if ($result) {
            if (mysqli_num_rows($result) > 0) {
                echo "<table class='styled-table'>
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
                        <tbody>";

                // Affichage des résultats de la recherche
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

                echo "</tbody></table>";
            } else {
                echo "<p>Aucun emprunt trouvé.</p>";
            }
        }
        ?>
    </div>
</body>
</html>
