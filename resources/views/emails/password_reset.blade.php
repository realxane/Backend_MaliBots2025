<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Réinitialisation du mot de passe</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background-color: #f7f5f5;
            color: #333;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 520px;
            margin: 50px auto;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            overflow: hidden;
        }
        .header {
            background-color: #A65B45;
            color: #fff;
            text-align: center;
            padding: 30px;
        }
        .header img {
            width: 80px;
            margin-bottom: 10px;
        }
        .content {
            padding: 30px;
            text-align: center;
        }
        .content h2 {
            color: #A65B45;
        }
        .button {
            display: inline-block;
            background-color: #A65B45;
            color: white;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 8px;
            margin-top: 20px;
        }
        .footer {
            text-align: center;
            font-size: 13px;
            color: #888;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <img src="{{ asset('logo_appli.png') }}" alt="Logo Anw Ka Dembe">
            <h1>Anw Ka Dembe</h1>
            <p>Découvrez l’âme du Mali</p>
        </div>
        <div class="content">
            <h2>Réinitialisation du mot de passe</h2>
            <p>Bonjour,</p>
            <p>Nous avons reçu une demande de réinitialisation de mot de passe pour votre compte.</p>
            <p>Veuillez cliquer sur le bouton ci-dessous pour créer un nouveau mot de passe :</p>
            <a href="{{ $url }}" class="button">Réinitialiser mon mot de passe</a>
            <p>Si vous n’êtes pas à l’origine de cette demande, ignorez simplement cet e-mail.</p>
        </div>
        <div class="footer">
            &copy; {{ date('Y') }} Anw Ka Dembe — Tous droits réservés.
        </div>
    </div>
</body>
</html>
