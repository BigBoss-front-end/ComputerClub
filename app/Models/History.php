<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class History extends Model
{
    use SoftDeletes, HasFactory;

    protected $fillable = ['position_id', 'count', 'is_revision', 'user_id', 'date', 'deleted_with_main',];
    
    public function position()
    {
        return $this->hasOne(Position::class, 'id', 'position_id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
}

