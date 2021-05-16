<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Article extends Model
{
    //
    protected $table = 'article';
    protected $fillable = ["userId", "offerType", "title", "description", "state"];
    public function getUser()
    {
        return $this->hasOne('App\User', "id", "userId");
    }

    public function Images()
    {
        return $this->hasMany('App\Image', "articleId", "articleId");
    }
}
