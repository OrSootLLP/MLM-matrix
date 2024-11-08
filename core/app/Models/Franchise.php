<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Traits\GlobalStatus;

class Franchise extends Model
{
    use SoftDeletes;
    use GlobalStatus;

    public function state()
    {
        return $this->belongsTo(State::class, 'state_id', 'state_id');
    }

    public function district()
    {
        return $this->belongsTo(District::class, 'district_id', 'districtid');
    }
}
