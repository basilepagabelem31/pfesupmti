<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; }
    </style>
</head>
<body>
    <p>Bonjour {{ $absence->stagiaire->nom }},</p>
    <p>Vous avez été marqué(e) comme absent(e) à la réunion :</p>
    <ul>
        <li>Date : {{ $absence->reunion->date->format('d/m/Y') }}</li>
        <li>Horaires : {{ $absence->reunion->heure_debut }} - {{ $absence->reunion->heure_fin }}</li>
        <li>Groupe : {{ $absence->reunion->groupe->nom }}</li>
    </ul>
    <p>Merci de prendre contact avec votre superviseur.</p>
</body>
</html>