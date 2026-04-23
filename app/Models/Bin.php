<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class Bin extends Model
{
    protected $primaryKey = 'bin_id'; // Ensure Laravel knows your custom PK

    /**
     * Get the human-readable status label based on percentage.
     */
    protected function statusLabel(): Attribute
    {
        return Attribute::make(
            get: function () {
                $percentage = $this->status;

                if ($percentage >= 90) return 'Full';
                if ($percentage >= 50) return 'Mid';
                return 'Empty';
            },
        );
    }
}