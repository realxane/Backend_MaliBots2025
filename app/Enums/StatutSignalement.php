<?php

namespace App\Enums;

enum StatutSignalement: string
{
    case Ouvert = 'ouvert';
    case EnCours = 'en_cours';
    case Traite = 'traite';
    case Ferme = 'ferme';
}