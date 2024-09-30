<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mission extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'name',
        'mission_type_id',
        'image',
        'required_user_level',
        'required_friends_invitation',
    ];

    public function getImageAttribute($value)
    {
          if (preg_match('/^https?:\/\//', $value)) {
       return $value; // If it's already a full URL, return it as is
    }
    else{
     return $value ? rtrim(env("APP_STORAGE_URL", "/"), '/') . '/' . ltrim($value, '/') : null;
     }


        // return $value ? env("APP_STORAGE_URL", "/") . '/storage' . $value : null;
    }

    public function levels()
    {
        return $this->hasMany(MissionLevel::class);
    }

    public function nextLevel()
    {
        return $this->hasOne(MissionLevel::class)
            ->orderBy('level');
    }

    public function type()
    {
        return $this->belongsTo(MissionType::class);
    }
}
