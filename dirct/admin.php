<!DOCTYPE html>
<html lang="fr" id="page2">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bibliothèque - Connexion</title>
    <link rel="stylesheet" href="admin.css">
</head>
<body>
    <div class="inscription ">
        <h3>Bonjour Monsieur le Directeur</h3>
        <form action="gestion.php" method="GET">
                 <div>
                    <button type="submit" class="btn">Gestions des utilisateurs</button>
                 </div>
        </form>
        <form action="notifications.php" method="GET">
                 <div>
                    <button type="submit" class="btn">Barre de notifications</button>
                 </div>
        </form>
        <form action="../principal/choix_gestion.php" method="GET">
                 <div>
                    <button type="submit" class="btn">Gestion bibliothéque</button>
                 </div>
        </form>

    </div>
</body>
</html>