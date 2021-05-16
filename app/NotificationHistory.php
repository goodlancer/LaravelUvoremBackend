<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class NotificationHistory extends Model
{
    //
    protected $table = "notification_history";
    protected $fillable = ['sender_id', 'receiver_id', 'group_id', 'title', 'content', 'image', 'type', 'answer'];

    public function send_user(){
        return $this->hasOne('App\User', 'id', 'sender_id');
    }
    public function receive_user(){
        return $this->hasOne('App\User', 'id', 'receiver_id');
    }
}
