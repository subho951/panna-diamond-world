<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BranchLead extends Model
{
    use SoftDeletes;

    public function leadActivities()
    {
        return $this->hasMany(LeadActivity::class, 'lead_sl_no', 'lead_sl_no');
    }
}
