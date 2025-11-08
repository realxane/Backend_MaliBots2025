<?php

namespace App\Enums;

enum StatutPaiement: string
{   
    case EN_ATTENTE = 'en_attente';
    case SUCCES = 'succes';
    case ECHEC = 'echec';
}