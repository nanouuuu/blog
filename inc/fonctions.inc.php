<?php
// fonction pour savoir si un utilisateur est connecté
function user_is_connected() {
    if( ! empty($_SESSION['utilisateur'])) {
        return true;
    } else {
        return false;
    }
}

// Fonction pour savoir si l'utilisateur a le statut admin
// front office : visuel de l'utilisateur / back office : gestion admin
function user_is_admin() {
    if (user_is_connected() && $_SESSION['utilisateur']['statut'] == 2) {
        return true;
    } return false; // pas de besoin du else, car si le true n'est pas éxecuté on arrive forcément sur false (pareil que la fonction précedente, autre syntaxe)
}