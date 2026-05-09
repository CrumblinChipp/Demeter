<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Pivot extends Model
{
    use HasFactory;

    protected $table = 'pivot';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'waste_entry_id',
        'bin_id',
        'weight',
        'entry_date',
    ];

    protected $casts = [
        'entry_date' => 'date',
        'weight' => 'float',
    ];

    /**
     * The waste entry this pivot record belongs to.
     */
    public function wasteEntry()
    {
        return $this->belongsTo(WasteEntry::class);
    }

    /**
     * The bin this pivot record belongs to.
     */
    public function bin()
    {
        return $this->belongsTo(Bin::class, 'bin_id', 'bin_id');
    }
}