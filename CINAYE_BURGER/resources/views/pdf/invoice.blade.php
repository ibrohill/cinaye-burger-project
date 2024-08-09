<!DOCTYPE html>
<html>
<head>
    <style>
    </style>
</head>
<body>
    <h1>Facture #{{ $commande->id }}</h1>
    <p>Client : {{ $commande->client_name }}</p>
    <p>Status : {{ $commande->etat }}</p>
    <p>Montant : {{ $commande->amount }}</p>
</body>
</html>
