<?php

namespace App\Actions;

use App\Models\Notification;

class NewNotification
{
    public static function handle($user,$other,$post, $body,$main_type, $type){

        $notification = new Notification();

        $notification->user_id = $user;
        $notification->person_id = $other;
        $notification->body = $body;
        $notification->main_type = $main_type;
        $notification->type = $type;
        $notification->data_id = $post;
        $notification->date = date('Y-m-d');
        $notification->time = strtotime(date('Y-m-d H:i:s'));
        $notification->save();
        return true;

    }
}