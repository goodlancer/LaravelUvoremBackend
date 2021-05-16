<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Connect extends Model
{
    //
    protected $table = 'connect';
    protected $fillable = ["user_id"];
}
