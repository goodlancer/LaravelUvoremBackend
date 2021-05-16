<?php

namespace App\Http\Controllers;

use App\User;
use App\Article;
use App\ArticleHistory;
use App\Image;
use App\ImageHistory;
use DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\UserRequest;
use App\Http\Controllers\EmailController;
use Illuminate\Http\Request;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\API\BaseController;

use App\NotificationHistory;
use App\Http\Controllers\NotificationController;
use Illuminate\Validation\Rules\Exists;

class AdminController extends Controller
{
    /**
     * Display a listing of the users
     *
     * @param  \App\User  $model
     * @return \Illuminate\View\View
     */
    private $firebase;
    private $emailController;
    private $notificationController;
    public function __construct()
    {
        $this->baseController = new BaseController;
        $this->firebase = new FirebaseController;
        $this->emailController = new EmailController;
        $this->notificationController = new NotificationController;
    }

    public function index(Request $request)
    {
        $date = now();
        $date->modify('-7 days');
        $formatted_date = $date->format('Y-m-d H:i:s');
        User::where('dt_premium', '<=', $formatted_date)->update(['role' => 2]);
        $country = $request->country;
        $users = null;
        if ($country == "0") {
            $users = User::where('role', 1)->orwhere('role', 4)->get();
        }
        else {
            $users = User::where('role', 1)->orwhere('role', 4)->where('country', $country)->get();
        }
        foreach ($users as $key => $value) {
            # code...
            $nowDate = now();
            // $firstDay = $nowDate->format("d");
            $date = strtotime($value->dt_premium);
            // $secondDay = getDate($date)["mday"];
            $limit = intval((strtotime($nowDate) - $date) / 86400);
            $users[$key]['active_count'] = count($value->getArticle->where('state', '=', 1));
            $users[$key]['diactive_count'] = count($value->getArticle->where('state', '=', 0));
            $users[$key]['dt_limit'] = 7 - $limit;
        }
        $countrys = DB::table('users')->groupBy('country')->get('country');

        $diactCount = Article::where('state', 0)->count();
        $actCount = Article::where('state', 1)->count();
        return view('admin.index', [
            'users' => $users,
            'countrys' => $countrys,
            'cur_country' => $request->country
        ]);
    }

    public function create()
    {
        return view('admin.create');
    }
    /**
     * Store a newly created user in storage
     *
     * @param  \App\Http\Requests\Request  $request
     * @param  \App\User  $model
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(UserRequest $request, User $model)
    {
        $image = "/uploads/avatar/default.png";
        $file = $request->file("photo_path");
        $user_id = $model->create($request->merge(['password' => Hash::make($request->get('password')), 'role' => 4, 'active' => 1, 'countryCode' => ""])->all())->id;

        $fileName = $user_id . '.png';
        $path = '/uploads/avatar/';
        if (!file_exists(public_path() . $path))
            mkdir(public_path() . $path, 0777, true);

        if ($file != null) {
            $file->move(public_path() . $path, $fileName);
        }
        else {
            try {
                copy(public_path() . "/uploads/avatar/default.png", public_path() . $path . $fileName);
            }
            catch (\Throwable $th) {
            }
        }
        $model->where('id', $user_id)->update(['avatar' => $path . $fileName]);

        return redirect()->route('admin.index', 'country=0')->withStatus(__('Admin successfully created.'));
    }
    /**
     * Show the form for editing the specified user
     *
     * @param  \App\User  $user
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        return view('admin.edit', [
            'user' => $user
        ]);
    }

    /**
     * Update the specified user in storage
     *
     * @param  \App\Http\Requests\Request  $request
     * @param  \App\User  $user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, User $user)
    {
        $active = $request->active == null ? 0 : 1;
        $nutrition_member = $request->nutrition_member == null ? 0 : 1;
        $password = $request->get('password');
        if ($password == "")
            $request->offsetUnset('password');
        else
            $request->merge(['password' => Hash::make($password)]);

        $file = $request->file("photo_path");

        if ($file != null) {
            $fileName = $user->id . '.png';
            $path = '/uploads/avatar/';
            $file->move(public_path() . $path, $fileName);
            $user->update(['avatar' => $path . $fileName]);
        }

        $user->update($request->merge(['active' => $active, 'nutrition_member' => $nutrition_member])->all());
        return redirect()->route('admin.index')->withStatus(__('User successfully updated.'));
    }

    public function destroy($id)
    {
        $model = new User;
        $model->where('id', $id)->delete();
        if (Article::where('userId', $id)->count() > 0) {
            $articleId = Article::where('userId', $id)->get('id')[0]->id;
            Article::where('userId', $id)->delete();
            Image::where('articleId', $articleId)->delete();
        }
        return back()->withStatus(__('User successfully deleted.'));
    }

    public function offerPublish(Request $request)
    {
        $id = $request->id;
        $state = $request->state;
        $userId = Article::where('id', $id)->get('userId')[0]->userId;
        $token = User::where('id', $userId)->get('device_token')[0]->device_token;
        $title = "";
        $content = "";
        if ($state == 0) {
            $title = "Warning";
            $content = "Your offer has been blocked by the administrator.";
        }
        else {
            $title = "Congratulations!";
            $content = "Your offers have just been published.";
        }

        if ($token != null) {
            $this->notificationController->send2Android($title, $content, $token);
        }
        $count = Article::where('userId', $userId)->count();
        if ($count >= 3) {
            Article::where('id', $id)->update(['state' => $state, 'active' => 0]);
        }
        else {
            Article::where('id', $id)->update(['state' => $state, 'active' => $state]);
        }
        $this->baseController->checkLimitOffer();
    }

    public function userActivate(Request $request)
    {
        $id = $request->id;
        $state = $request->state;
        User::where('id', $id)->update(['active' => $state]);
    }

    public function getAllOffers(Request $request)
    {
        $id = $request->id;
        $offerType = $request->offerType;
        $arrArticles = Article::where('userId', $id)->where('offerType', $offerType)->orderByDesc('updated_at')->get();
        if ($offerType == 3) {
            $arrArticles = Article::where('userId', $id)->orderByDesc('updated_at')->get();
        }
        $arrImages = [];

        for ($i = 0; $i < count($arrArticles); $i++) {
            $articleId = $arrArticles[$i]->id;
            $images = Image::where('articleId', $articleId)->get('imagePath');
            $arrImages[$i] = $images;
        }
        return ['articles' => $arrArticles, 'images' => $arrImages];

    }

    public function getUsers(Request $request)
    {
        $country = $request->country;
        $users = null;
        if ($country == "all") {
            $users = DB::table('users')->get();
        }
        else {
            $users = User::where('country', $country)->get();
        }
        $countrys = DB::table('users')->groupBy('country')->get('country');
        return ['users' => $users, 'countrys' => $countrys];
    }

    public function articleDelete($id)
    {
        $article = Article::where('id', $id)->get()[0];
        $userId = $article->userId;
        $token = User::where('id', $userId)->get('device_token')[0]->device_token;
        $title = "";
        $content = "";
        $title = "Warning!";
        $content = "Your offer has been deleted by the administrator.";
        if ($token != null) {
            $this->notificationController->send2Android($title, $content, $token);
        }
        $images = Image::where('articleId', $id)->get();
        $articleHistory = (['userId'=> $article->userId, 'offerType' => $article->offerType, 'title' => $article->title, 'description' => $article->description, 'state' => $article->state, 'active' => $article->active]);
        ArticleHistory::create($articleHistory);
        $articleId = ArticleHistory::max('id');
        foreach ($images as $key => $image) {
            $imageHistory = (['articleId' => $articleId, 'imagePath' => $image->imagePath]);
            ImageHistory::create($imageHistory);
        }
        Article::where('id', $id)->delete();
        Image::where('articleId', $id)->delete();
        return back();
    }

    public function countryState(Request $request)
    {
        $id = $request->id;
        $state = $request->state;
        $new_country = User::where('id', $id)->get('new_country');
        $new_countryCode = User::where('id', $id)->get('new_countryCode');
        if ($state == 0) {
            User::where('id', $id)->update(['change_country' => 0]);
        }
        else if ($state == 1) {
            User::where('id', $id)->update(['change_country' => 1, 'country' => $new_country[0]['new_country'], 'countryCode' => $new_countryCode[0]['new_countryCode']]);
        }
        else if ($state == 2) {
            User::where('id', $id)->update(['change_country' => 2]);
        }
        $token = User::where('id', $id)->get('device_token')[0]->device_token;
        $title = "";
        $content = "";
        $title = "Success";
        if ($state == 0) {
            $content = "Your request to change the country of the account has been refused.";
        }
        else if ($state == 1) {
            $content = "Your request to change the country of the account has been approved.";
        }
        if ($token != null && $content != "") {
            $this->notificationController->send2Android($title, $content, $token);
        }
    }
}
