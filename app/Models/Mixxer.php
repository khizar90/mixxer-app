<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Mixxer extends Model
{
    use HasFactory;

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    protected $attributes = [
        'photos' => '',
        'doc' => '',
        'address' => '',
        'viewer_url' => '',
        'host_url' => '',


    ];
    protected $hidden = [
        'updated_at',
    ];
}
