<?php
include("../database.php"); // Inclure la connexion à la base de données

// Fonction pour masquer le mot de passe
function maskPassword($password) {
    $length = strlen($password);
    return str_repeat('*', max(0, $length - 2)) . substr($password, -2);
}

// Fonction de recherche
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "
    SELECT id_user, email, password, autorisation 
    FROM user 
    WHERE email LIKE ? OR autorisation LIKE ? OR id_user LIKE ?
";
$stmt = $conn->prepare($sql);

$searchTerm = "%$search%";
$stmt->bind_param("sss", $searchTerm, $searchTerm, $searchTerm);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Utilisateurs</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background: linear-gradient(to right,rgb(113, 222, 202), #9face6);
        }
        h1 {
            text-align: center;
            color: #333;
        }
        .warning {
            background-color: #fff3cd;
            border: 1px solid #ffeeba;
            padding: 15px;
            margin-bottom: 20px;
            color: #856404;
            border-radius: 5px;
        }
        form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
        }
        form input[type="text"] {
            width: 300px;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            margin-right: 10px;
        }
        form input[type="submit"] {
            padding: 10px 20px;
            border: none;
            background-color: #007bff;
            color: white;
            border-radius: 5px;
            cursor: pointer;
        }
        form input[type="submit"]:hover {
            background-color: #0056b3;
        }
        button {
            display: inline-flex;
            align-items: center;
            padding: 8px 12px;
            margin: 5px;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            text-decoration: none;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
        .delete-button {
            background-color: #dc3545;
        }
        .delete-button:hover {
            background-color: #a71d2a;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 12px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        td:last-child {
            text-align: center;
        }
        .icon {
            margin-right: 5px;
        }
    </style>
    <script>
        function openUserForm(url) {
            window.open(url, 'UserForm', 'width=600,height=400');
        }
        function deleteUser(id) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                window.location.href = 'supprimer.php?id_user=' + id;
            }
        }
    </script>
</head>
<body>
    <h1><i class="fas fa-users"></i> Gestion des Utilisateurs</h1>
    
    <div class="warning">
        <strong><i class="fas fa-exclamation-triangle"></i> Avertissement de sécurité :</strong> L'affichage des mots de passe, même masqués, est une pratique dangereuse. 
        Cela peut compromettre la sécurité de vos utilisateurs. Envisagez de supprimer cette fonctionnalité dans un environnement de production.
    </div>

    <form action="" method="GET">
        <input type="text" name="search" placeholder="Rechercher un utilisateur" value="<?php echo htmlspecialchars($search); ?>">
        <input type="submit" value="Rechercher">
    </form>

    <button onclick="openUserForm('ajouter.php')">
        <i class="fas fa-plus icon"></i> Ajouter un utilisateur
    </button>

    <table>
        <tr>
            <th>ID Utilisateur</th>
            <th>Email</th>
            <th>Mot de passe (masqué)</th>
            <th>Autorisation</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['id_user']); ?></td>
            <td><?php echo htmlspecialchars($row['email']); ?></td>
            <td><?php echo maskPassword($row['password']); ?></td>
            <td><?php echo htmlspecialchars($row['autorisation']); ?></td>
            <td>
                <button onclick="openUserForm('modifier.php?id_user=<?php echo $row['id_user']; ?>')">
                    <i class="fas fa-edit icon"></i> Modifier
                </button>
                <button class="delete-button" onclick="deleteUser(<?php echo $row['id_user']; ?>)">
                    <i class="fas fa-trash icon"></i> Supprimer
                </button>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
