<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ArticleHistory extends Model
{
    //
    protected $table = 'article_history';
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
