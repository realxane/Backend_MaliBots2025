<?php

namespace App\Enums;

enum Role: string
{
    case Acheteur = 'acheteur';
    case Vendeur = 'vendeur';
    case Admin = 'admin';
} 