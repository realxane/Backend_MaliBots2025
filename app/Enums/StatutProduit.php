<?php

namespace App\Enums;

enum StatutProduit: string
{
    case EnAttente = 'en_attente';
    case Valide = 'valide';
    case Refuse = 'refuse';
    case Supprime = 'supprime';
}