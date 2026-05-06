<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class pivot extends Model
{
    use HasFactory;

    protected $table = 'pivot';
    protected $primaryKey = 'id';

    protected $fillable = [
      'date',
      'weight',
    ];


}