<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Interface Stagiaire</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(135deg, #f8f9fa, #e9ecef);
            color: #333;
            text-align: center;
        }

        h1 {
            margin: 20px 0;
            font-size: 2.5em;
            color: #007bff;
        }

        .button-container {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin-top: 50px;
        }

        .button-container button {
            background-color: #007bff;
            color: white;
            border: none;
            border-radius: 50%;
            width: 150px;
            height: 150px;
            font-size: 1em;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            text-align: center;
            flex-direction: column;
            gap: 5px;
        }

        .button-container button:hover {
            background-color: #0056b3;
            transform: translateY(-5px);
        }

        .button-container button i {
            font-size: 2em;
        }

        .button-container button span {
            font-size: 0.9em;
        }

        footer {
            margin-top: 50px;
            font-size: 0.9em;
            color: #777;
        }
    </style>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>
    <h1>Interface de Stagiaire</h1>

    <div class="button-container">
        <button onclick="window.location.href='../principal/emprunt/liste_emprunt.php';">
            <i class="fas fa-book"></i>
            <span>Gérer Emprunts</span>
        </button>
        <button onclick="window.location.href='retours.php';">
            <i class="fas fa-undo"></i>
            <span>Retourner Exemplaire</span>
        </button>
    </div>

    <footer>
        &copy; <?php echo date("Y"); ?> Interface Stagiaire. Tous droits réservés.
    </footer>
</body>
</html>
