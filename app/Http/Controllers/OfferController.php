<?php

namespace App\Http\Controllers;

use App\User;
use App\Article;
use App\Image;
use DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\EmailController;
use Illuminate\Http\Request;
use App\Http\Controllers\FirebaseController;

use App\NotificationHistory;
use Illuminate\Validation\Rules\Exists;

class OfferController extends Controller
{
    /**
     * Display a listing of the users
     *
     * @param  \App\User  $model
     * @return \Illuminate\View\View
     */
    private $firebase;
    private $emailController;
    public function __construct()
    {
        $this->firebase = new FirebaseController;
        $this->emailController = new EmailController;
    }
    
    public function index(Request $request)
    {
        // $user = DB::table('users')
        //     ->select(DB::raw('users.*, count(article.*)'))
        //     ->join('article', 'users.id', '=', 'article.userId')    
        //     ->where('article.state', '=', 0)
        //     ->get();
        // foreach ($user as $key => $value) {
        // //     # code...
        // $user[$key]['count']  = count($value->getArticle);
        //     // $value->getArticle;
        // }
        // dd($user->all());
        // exit();
        $type = $request->type;
        $data = null;
        if($type >= 0){
            $data = DB::select('SELECT users.*, article.title, article.description, article.state, article.active as art_active,  article.id as article_id, article.offerType FROM article 
            LEFT JOIN users ON users.id = article.userId WHERE users.role != 1
             WHERE article.offerType = '.$type.' ORDER BY article.updated_at DESC');
        }else {
            $data = DB::select('SELECT users.*, article.title, article.description, article.state, article.active as art_active,  article.id as article_id, article.offerType FROM article 
            LEFT JOIN users ON users.id = article.userId WHERE users.role != 1 ORDER BY article.updated_at DESC');
        }
        $arrImages = [];
        for($i = 0; $i < count($data); $i ++){
            $articleId = $data[$i]->article_id;
            $images = Image::where('articleId', $articleId)->get('imagePath');
            $arrImages[$i] = $images;
        }
        return view('offers.index', [
            'data' => $data,
            'images' => $arrImages
        ]);
    }

    public function edit (Request $request){
        $id = $request->article_id;
        $type = $request->offer_type;
        $title = $request->title;
        $description = $request->description;
        Article::where('id', $id)->update(['title' => $title, 'description' => $description, 'offerType' => $type]);
        return back();
    }
}
