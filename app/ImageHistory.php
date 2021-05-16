<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class ImageHistory extends Model
{
    //
    protected $table = 'image_history';
    protected $fillable = ["imagePath", "articleId"];
}
