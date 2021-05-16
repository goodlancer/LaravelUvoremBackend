<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use Illuminate\Http\Request;
use Validator;
use Hash;
use Storage;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\API\BaseController;
use App\User;
use App\Article;
use App\ArticleHistory;
use App\Image;
use App\ImageHistory;
use DB;

class ArticleController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    private $firebase;
    private $email;
    public function __construct()
    {
        $this->baseController = new BaseController;
        $this->firebase = new FirebaseController;
        $this->email = new EmailController;
        $this->notificationController = new NotificationController;
    }

    public function saveImage(Request $request) {
        $userId = $request->get('userId');
        $file_name_1 = null;
        $file_name_2 = null;
        $file_name_3 = null;
        try {
            $path = '/uploads/articles/' . $userId . '/';
            if (!file_exists(public_path() . $path)) {
                mkdir(public_path() . $path, 0777, true);
            }
            $image1 = $request->file( 'image_1' );
            $image2 = $request->file( 'image_2' );
            $image3 = $request->file( 'image_3' );
            $file_name_1 = $path . time() . '_1' . '.' . $image1->extension();
            $file_name_2 = $path . time() . '_2' . '.' . $image2->extension();
            $file_name_3 = $path . time() . '_3' . '.' . $image3->extension();
            $image1->move( public_path() . $path, $file_name_1 );
            $image2->move( public_path() . $path, $file_name_2 );
            $image3->move( public_path() . $path, $file_name_3 );
        }
        catch (\Throwable $th) {
            return response()->json(['success' => false], 200);
        }
        $articleId = Article::max('id');
        $input = ['articleId' => $articleId, 'imagePath' => $file_name_1];
        Image::create($input);
        $input = ['articleId' => $articleId, 'imagePath' => $file_name_2];
        Image::create($input);
        $input = ['articleId' => $articleId, 'imagePath' => $file_name_3];
        Image::create($input);
    }

    public function insertArticle(Request $request)
    {
        $input = $request->merge(['state' => 0])->all();
        Article::create($input);

        $this->baseController->checkLimitOffer();
        return response()->json(['success' => true], 200);
    }

    public function getAllArticle(Request $request)
    {
        $this->checkUser();
        $offerType = $request->offerType;
        $id = $request->userId;
        $userType = $request->userType;
        $date = now();
        $role = 2;
        if ($userType == 0) {
            $date->modify('-420 minutes');
            $role = 3;
        }
        else {
            $date->modify('-60 minutes');
            $role = 2;
        }
        $formatted_date = $date->format('Y-m-d H:i:s');
        $articles = Article::where('active', '=', 1)->orderByDesc('userId')->get();
        // foreach ($articles as $key => $value) {
        for ($i = 0; $i < count($articles); $i++) {
            # code...
            if ($articles[$i]->updated_at < $formatted_date) {
                $articles[$i]->where('id', '=', $articles[$i]->id)->update(['active' => 0]);
                $token = User::where('id', $articles[$i]->userId)->get('device_token')[0]->device_token;
                // $userRole = User::where('id', '=', $value->userId)->first('role')->role;
                // $count = Article::where('active', '=', 0)->where('userId', '=', $value->userId)->count();
                // if($count > 5){
                //     if($userRole == 2){
                //         $article = Article::orderBy('updated_at', 'asc')->first('id')->id;
                //         Article::where('id', $article)->delete();
                //     }
                // }
                if ($i > 0) {
                    if (($articles[$i]->userId == $articles[$i - 1]->userId) && ($articles[$i - 1]->updated_at < $formatted_date)) {
                        continue;
                    }
                }
                $title = "";
                $content = "";
                $title = "Warning";
                $content = "Your offer has just been offline, automatically repost the offer in the \"PREVIOUS OFFER PUBLISHED\" page.";
                if ($token != null) {
                    $this->notificationController->send2Android($title, $content, $token);
                }
            }
        }
        //users.id != '.$id.' AND
        $data = DB::select('SELECT users.*, article.title, article.description, article.updated_at,  image.imagePath FROM users
        LEFT JOIN article ON users.id = article.userId LEFT JOIN image ON image.articleId = article.id WHERE users.role = ' . $role . ' AND article.offerType = ' . $offerType . ' AND article.active = 1 AND article.state = 1 AND article.updated_at > "' . $formatted_date . '" ORDER BY article.updated_at DESC');
        // $data = DB::select('SELECT users.*, article.title, article.description, article.updated_at,  image.imagePath FROM users
        // LEFT JOIN article ON users.id = article.userId LEFT JOIN image ON image.articleId = article.id ');

        return response()->json(['success' => true, 'data' => $data, 'start_time' => $formatted_date], 200);
    }
    public function getAticle(Request $request){
        $this->checkUser();
        $articleId = $request->aticleId;
        $data = DB::select('SELECT article.*, image.imagePath, users.name, users.email, users.country FROM article LEFT JOIN image on image.articleId=article.id LEFT JOIN users on users.id=article.userId WHERE article.id='.$articleId);
        $images = array();
        foreach ($data as $value) {
            array_push($images, $value->imagePath);
        }
        $articleObject = [
            'id' => $data[0]->id,
            'userId' => $data[0]->userId,
            'title' => $data[0]->title,
            'description' => $data[0]->description,
            'state' => $data[0]->state,
            'createdAt' => $data[0]->created_at,
            'updated_at' => $data[0]->updated_at,
            'name' => $data[0]->name,
            'email' => $data[0]->email,
            'country' => $data[0]->country,
            'images' => $images,
        ];
        return response()->json(['success' => true, 'data' => $articleObject], 200);
    }
    public function getAticleList(Request $request){
        $userId = $request->userId;
        $data = DB::select('SELECT article.*, image.imagePath, users.country FROM article LEFT JOIN image ON image.articleId = article.id LEFT JOIN users ON users.id = article.userId  ORDER BY article.updated_at DESC');
        $convertedData =  array();
        if($data){
            $ItemId = $data[0];
            $itemArray = array();
            // $itemObject = {};
            foreach ($data as $value) {
                if($ItemId->id != $value->id){
                    $ItemId = $value;
                    $itemObject->images = $itemArray;
                    array_push($convertedData, $itemObject);
                    $itemArray = array();
                    array_push($itemArray,$value->imagePath);
                }else{
                    $itemObject = (object) [
                        'id' => $value->id,
                        'offerType' => $value->offerType,
                        'country' => $value->country,
                        'title' => $value->title,
                        'description' => $value->description,
                        'images' => $itemArray,
                      ];

                    
                    array_push($itemArray,$value->imagePath);
                }
            }
            $itemObject->images = $itemArray;
            array_push($convertedData, $itemObject);
        }
        return response()->json(['success' => true, 'data' => $convertedData], 200);
    }

    public function getAllOwnerArticle(Request $request)
    {
        $id = $request->userId;
        $offerType = $request->offerType;
        $role = $request->role;
        $data = [];
        $count = Article::where(['userId' => $id, 'active' => 1, 'state' => 1])->count();
        if ($role == 3) {
            $data = DB::table('users')
                ->join('article', 'users.id', '=', 'article.userId')
                ->join('image', 'image.articleId', '=', 'article.id')
                ->select('users.*', 'article.id as article_id', 'article.title', 'article.description', 'image.imagePath')
                ->where('users.id', '=', $id)
                ->where('article.state', '=', 1)
                ->where('article.active', '=', 0)
                ->orderByDesc('updated_at')
                ->get();
        }
        else {
            $data = DB::table('users')
                ->join('article', 'users.id', '=', 'article.userId')
                ->join('image', 'image.articleId', '=', 'article.id')
                ->select('users.*', 'article.id as article_id', 'article.title', 'article.description', 'image.imagePath')
                ->where('users.id', '=', $id)
                ->where('article.state', '=', 1)
                ->where('article.active', '=', 0)
                ->orderByDesc('updated_at')
                ->limit((3 - $count) * 3)
                ->get();
        }
        return response()->json(['success' => true, 'data' => $data], 200);
    }

    public function getOwnerArticleCount(Request $request)
    {
        $id = $request->userId;
        $count = Article::where('userId', '=', $id)->where('state', '=', 1)->count();
        return response()->json(['success' => true, 'count' => $count], 200);
    }

    public function deleteArticle(Request $request)
    {
        $id = $request->id;
        $article = Article::where('id', $id)->get()[0];
        $images = Image::where('articleId', $id)->get();
        $articleHistory = (['userId' => $article->userId, 'offerType' => $article->offerType, 'title' => $article->title, 'description' => $article->description, 'state' => $article->state, 'active' => $article->active]);
        ArticleHistory::create($articleHistory);
        $articleId = ArticleHistory::max('id');
        foreach ($images as $key => $image) {
            $imageHistory = (['articleId' => $articleId, 'imagePath' => $image->imagePath]);
            ImageHistory::create($imageHistory);
        }
        Article::where('id', $id)->delete();
        Image::where('articleId', $id)->delete();
        return response()->json(['success' => true], 200);
    }
    public function deleteExtraArticles(Request $request)
    {
        $id = $request->id;
        $count = Article::where('userId', $id)->count() - 2;
        $extraArticle = DB::select('SELECT * FROM article WHERE userId = ' . $id . ' ORDER BY article.updated_at ASC LIMIT ' . $count);
        foreach ($extraArticle as $key => $value) {
            $articleId = $value->id;
            $article = Article::where('id', $articleId)->get()[0];
            $images = Image::where('articleId', $articleId)->get();
            $articleHistory = (['userId' => $article->userId, 'offerType' => $article->offerType, 'title' => $article->title, 'description' => $article->description, 'state' => $article->state, 'active' => $article->active]);
            ArticleHistory::create($articleHistory);
            $articleId = ArticleHistory::max('id');
            foreach ($images as $key => $image) {
                $imageHistory = (['articleId' => $articleId, 'imagePath' => $image->imagePath]);
                ImageHistory::create($imageHistory);
            }
            Article::where('id', $articleId)->delete();
            Image::where('articleId', $articleId)->delete();
        }
        return response()->json(['success' => true], 200);
    }
    public function republicOffer($id)
    {
        Article::where('id', $id)->update(['active' => 1]);
        return response()->json(['success' => true], 200);
    }

    public function checkUser()
    {
        $date = now();
        $date->modify('-31 days');
        $formatted_date = $date->format('Y-m-d H:i:s');
        User::where('dt_premium', '<=', $formatted_date)->update(['role' => 2]);
    }

    public function updateRating($id) {
        $rating = Article::where('id', $id)->get()[0]->rating;
        Article::where('id', $id)->update(['rating' => $rating + 1]);
    }
}
