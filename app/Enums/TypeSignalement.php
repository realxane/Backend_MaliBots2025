<?php

namespace App\Enums;

enum TypeSignalement: string
{
    case Produit = 'Produit';
    case Musique = 'Musique';
    case Photo = 'Photo';
    case Conte = 'Conte';
    case Proverbe = 'Proverbe';
    case Utilisateur = 'Utilisateur';
}