<?php

namespace App\Enums;

enum TableLocation: string
{
    case Buy = 'buy';
    case Rent = 'rent';
    case Lease = 'lease';
}