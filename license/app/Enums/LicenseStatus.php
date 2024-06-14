<?php

namespace App\Enums;

enum LicenseStatus: string
{
    case Active = 'Active';
    case Inactive = 'Inactive';
    case Expired = 'Expired';
}
