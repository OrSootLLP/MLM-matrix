<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\GlobalStatus;

class Product extends Model
{
    use SoftDeletes;
    use GlobalStatus;
}
