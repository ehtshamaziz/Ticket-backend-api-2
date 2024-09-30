<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MissionLevel extends Model
{
    use HasFactory;

    protected $fillable = [
        'level',
        'cost',
        'production_per_hour',
        'mission_id', 
    ];

    public function mission()
    {
        return $this->belongsTo(Mission::class);
    }
}
