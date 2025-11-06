<?php

namespace App\Enums;

enum StatutCommande: string
{
    case EnCours = 'en_cours';
    case Payee = 'payee';
    case Annulee = 'annulee';
}