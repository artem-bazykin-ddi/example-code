<?php
declare(strict_types=1);

namespace App\Manager;

use Illuminate\Database\Eloquent\Model;

interface ManagerInterface
{
    public static function getModel();
}