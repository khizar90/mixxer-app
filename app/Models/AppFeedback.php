<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppFeedback extends Model
{
    use HasFactory;
    protected $hidden = [
        'updated_at',
        'created_at'
    ];
    public function user(){
        return $this->belongsTo(User::class, 'user_id'); 
    }
}