<?php

namespace App\Enums;

enum MethodePaiement: string
{
    case OrangeMoney = 'OrangeMoney';
    case Wave = 'Wave';
    case Mastercard = 'Mastercard';
}