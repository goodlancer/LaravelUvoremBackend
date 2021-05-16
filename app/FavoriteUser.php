<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class FavoriteUser extends Model
{
    //
    protected $fillable = ['userid', 'favour_userid'];

    protected function user()
    {
        return $this->hasOne('App\User', 'id', 'userid');
    }
    protected function favour_user()
    {
        return $this->hasOne('App\User', 'id', 'favour_userid');
    }
}
