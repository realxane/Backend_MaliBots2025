<?php

namespace App\Enums;

enum StatutPaiement: string
{
    case Initie = 'initie';
    case Reussi = 'reussi';
    case Echoue = 'echoue';
}