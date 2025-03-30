<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothécaire Principal</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(to bottom, #007bff, #f4f4f9);
            color: #fff;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        h1 {
            text-align: center;
            font-size: 2.5em;
            text-shadow: 2px 2px 5px rgba(0, 0, 0, 0.5);
        }

        .button-container {
            display: grid;
            grid-template-columns: repeat(3, 1fr); /* 3 colonnes */
            gap: 30px; /* Espace entre les boutons */
            justify-items: center;
            align-items: center;
            margin-top: 20px;
        }

        .button {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            width: 150px;
            height: 150px;
            text-align: center;
            font-size: 16px;
            color: #fff;
            background-color: #007bff;
            border: none;
            border-radius: 50%; /* Cercle */
            cursor: pointer;
            text-decoration: none;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
            transition: transform 0.3s, box-shadow 0.3s, background-color 0.3s;
        }

        .button i {
            font-size: 40px;
            margin-bottom: 10px;
        }

        .button:hover {
            background-color: #0056b3;
            transform: translateY(-5px);
            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
        }

        .button:active {
            transform: translateY(2px);
            box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
        }

        footer {
            text-align: center;
            margin-top: 30px;
            font-size: 0.9em;
            color: whitesmoke;
        }
    </style>
</head>
<body>
    <h1>Bienvenue, Bibliothécaire Principal</h1>
    
    <div class="button-container">
        <?php
        // Simuler des permissions provenant d'une base de données
        $permissions = [
            'emprunts' => true,
            'utilisateurs' => true,
            'documents' => true,
            'exemplaires' => true,
            'retours' => true,
            'rappels' => true
        ];
        ?>

        <!-- Boutons dynamiques -->
        <?php if ($permissions['emprunts']): ?>
        <button class="button" onclick="navigateTo('emprunt/liste_emprunt.php')">
            <i class="fas fa-book-reader"></i>
            Gérer les emprunts
        </button>
        <?php endif; ?>

        <?php if ($permissions['utilisateurs']): ?>
        <button class="button" onclick="navigateTo('utilisateur/gerer_utilisateurs.php')">
            <i class="fas fa-users"></i>
            Gérer les utilisateurs
        </button>
        <?php endif; ?>

        <?php if ($permissions['documents']): ?>
        <button class="button" onclick="navigateTo('document/liste_document.php')">
            <i class="fas fa-file-alt"></i>
            Gérer les documents
        </button>
        <?php endif; ?>

        <?php if ($permissions['exemplaires']): ?>
        <button class="button" onclick="navigateTo('exemplire/liste_exemplire.php')">
            <i class="fas fa-book"></i>
            Gérer les exemplaires
        </button>
        <?php endif; ?>

        <?php if ($permissions['retours']): ?>
        <button class="button" onclick="navigateTo('retour.php')">
            <i class="fas fa-undo-alt"></i>
            Gérer les retours
        </button>
        <?php endif; ?>

        <?php if ($permissions['rappels']): ?>
        <button class="button" onclick="navigateTo('rappels.php')">
            <i class="fas fa-bell"></i>
            envoyer les rappels
        </button>
        <?php endif; ?>
    </div>

    <footer>
        &copy; 2024 Bibliothèque Numérique. Tous droits réservés.
    </footer>

    <script>
        /**
         * Fonction pour naviguer vers une page après confirmation.
         * @param {string} url - L'URL de la page cible.
         */
        function navigateTo(url) {
            const confirmAction = confirm("Voulez-vous vraiment accéder à cette section ?");
            if (confirmAction) {
                window.location.href = url;
            }
        }
    </script>
</body>
</html>
