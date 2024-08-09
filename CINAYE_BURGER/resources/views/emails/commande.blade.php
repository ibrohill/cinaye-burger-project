<!DOCTYPE html>
<html>
<head>
    <title>Confirmation de Commande</title>
</head>
<body>
    <h1>Merci pour votre commande, {{ $commande->client_nom }}!</h1>
    <p>Vous avez commandé un burger : {{ $commande->burger_nom }}.</p>
    <p>Montant total: {{ $commande->montant }} fr CFA </p>
    <p>Status de la commande: {{ $commande->etat }}</p>
    <p>Date de la commande: {{ $commande->date_commande->format('d/m/Y H:i') }}</p>
</body>
</html>
