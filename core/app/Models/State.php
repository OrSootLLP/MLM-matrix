<?php

namespace App\Models;

use App\Traits\GlobalStatus;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use GlobalStatus;

    public function districts()
    {
        return $this->hasMany(District::class, 'state_id');
    }
}
