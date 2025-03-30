<?php
session_start(); // Démarrer la session pour utiliser les messages

// Vérifier si un message existe dans la session
if (isset($_SESSION['message'])) {
    // Récupérer et afficher le message
    $message = $_SESSION['message'];
    $message_type = $_SESSION['message_type'];

    // Afficher le message avec une classe spécifique selon le type
    echo "<div class='message $message_type'>$message</div>";

    // Effacer le message après l'affichage pour ne pas le dupliquer
    unset($_SESSION['message']);
    unset($_SESSION['message_type']);
}
?>

<style>
    .message {
        padding: 10px;
        margin: 20px 0;
        border-radius: 5px;
        text-align: center;
        font-weight: bold;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }
    .success {
        background-color: #d4edda;
        color: #155724;
        border: 1px solid #c3e6cb;
    }
    .error {
        background-color: #f8d7da;
        color: #721c24;
        border: 1px solid #f5c6cb;
    }
</style>

<?php
include("../../database.php"); // Inclure la connexion à la base de données

// Fonction pour rechercher les utilisateurs par tous les attributs
$search = isset($_GET['search']) ? $_GET['search'] : '';
$sql = "
    SELECT CNI, nom, prenom, type_abonnement, emprunts_en_cours, peut_emprunter 
    FROM UTILISATEUR 
    WHERE CNI LIKE ? 
       OR nom LIKE ? 
       OR prenom LIKE ? 
       OR type_abonnement LIKE ? 
       OR emprunts_en_cours LIKE ? 
       OR peut_emprunter LIKE ?";
$stmt = $conn->prepare($sql);

// Préparer la recherche pour correspondances partielles
$searchTerm = "%$search%";
$stmt->bind_param("ssssss", $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm);
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
            color: #333;
        }
        h1 {
            text-align: center;
            color:rgb(253, 254, 255);
            margin-bottom: 20px;
            text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.1);
        }
        form {
            display: flex;
            justify-content: center;
            margin-bottom: 20px;
            gap: 10px;
        }
        form input[type="text"] {
            padding: 10px;
            width: 300px;
            border: 1px solid #ddd;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        form button {
            padding: 10px 15px;
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        form button:hover {
            background-color: #0056b3;
        }
        .action-buttons {
            display: flex;
            justify-content: flex-start;
            margin-bottom: 20px;
        }
        .action-buttons button {
            padding: 10px 15px;
            margin-right: 10px;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .action-buttons button:hover {
            opacity: 0.9;
        }
        .add-button {
            background-color: #28a745;
        }
        .add-button:hover {
            background-color: #218838;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #007bff;
            color: white;
        }
        td {
            background-color: #fff;
        }
        td:last-child {
            text-align: center;
        }
        button {
            padding: 8px 10px;
            margin: 2px;
            color: white;
            font-size: 14px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        .edit-button {
            background-color: #ffc107;
        }
        .edit-button:hover {
            background-color: #e0a800;
        }
        .delete-button {
            background-color: #dc3545;
        }
        .delete-button:hover {
            background-color: #b21f2d;
        }
    </style>
    <script>
        function openUserForm(url) {
            window.open(url, 'UserForm', 'width=600,height=400');
        }
        function deleteUser(CNI) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet utilisateur ?')) {
                window.location.href = 'supprimer_utilisateur.php?CNI=' + CNI;
            }
        }
    </script>
</head>
<body>
    <h1>Gestion des Utilisateurs</h1>

    <form action="" method="GET">
        <input type="text" name="search" placeholder="Rechercher un utilisateur" value="<?php echo htmlspecialchars($search); ?>">
        <button type="submit">
            <i class="fas fa-search"></i> Rechercher
        </button>
    </form>

    <div class="action-buttons">
        <button class="add-button" onclick="openUserForm('ajouter_utilisateur.php')">
            <i class="fas fa-user-plus"></i> Ajouter un utilisateur
        </button>
    </div>

    <table>
        <tr>
            <th>CNI</th>
            <th>Nom</th>
            <th>Prénom</th>
            <th>Type d'abonnement</th>
            <th>Emprunts en cours</th>
            <th>Peut emprunter</th>
            <th>Action</th>
        </tr>
        <?php while ($row = $result->fetch_assoc()): ?>
        <tr>
            <td><?php echo htmlspecialchars($row['CNI']); ?></td>
            <td><?php echo htmlspecialchars($row['nom']); ?></td>
            <td><?php echo htmlspecialchars($row['prenom']); ?></td>
            <td><?php echo htmlspecialchars($row['type_abonnement']); ?></td>
            <td><?php echo htmlspecialchars($row['emprunts_en_cours']); ?></td>
            <td><?php echo $row['peut_emprunter'] ? "Oui" : "Non"; ?></td>
            <td>
                <button class="edit-button" onclick="openUserForm('modifier_utilisateur.php?CNI=<?php echo $row['CNI']; ?>')">
                    <i class="fas fa-edit"></i> Modifier
                </button>
                <button class="delete-button" onclick="deleteUser('<?php echo $row['CNI']; ?>')">
                    <i class="fas fa-trash"></i> Supprimer
                </button>
            </td>
        </tr>
        <?php endwhile; ?>
    </table>
</body>
</html>
