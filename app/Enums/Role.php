<?php

namespace App\Enums;

enum Role: string
{
    case Acheteur = 'Acheteur';
    case Vendeur = 'Vendeur';
    case Admin = 'Admin';
} 
