<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReportUser extends Model
{
    use HasFactory;
    protected $attributes = [
        'media' => '',
        'doc' => '',
        'specify_affect' => ''
    ];
    public function user(){
        return $this->belongsTo(User::class, 'user_id'); 
    }
    public function report(){
        return $this->belongsTo(User::class, 'reported_id'); 
    }
}
