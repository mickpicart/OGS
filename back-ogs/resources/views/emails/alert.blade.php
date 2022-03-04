<!DOCTYPE html>
<html lang="fr">
  <head>
    <meta charset="utf-8">
  </head>
  <body>
    <h2>Anomalie détectée dans les en-têtes envoyées par le serveur en réponse à une requête HTTP</h2>
    <p>Le site {{ $alert['website'] }} semble avoir des problèmes :</p>
    <p>{{ $alert['get_header_response']['0'] }}</p>
  </body>
</html>
