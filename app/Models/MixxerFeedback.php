<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MixxerFeedback extends Model
{
    use HasFactory;
    protected $hidden = [
        'updated_at',
        'created_at'
    ];
    public function user(){
        return $this->belongsTo(User::class, 'user_id'); 
    }
    public function mixxer(){
        return $this->belongsTo(Mixxer::class, 'mixxer_id'); 
    }
}
