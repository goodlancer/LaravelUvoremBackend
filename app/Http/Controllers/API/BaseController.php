<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Controllers\FirebaseController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\EmailController;
use Illuminate\Http\Request;
use App\FavoriteUser;
use App\Connect;
use App\User;
use App\Article;
use Auth;
use DB;

class BaseController extends Controller
{
    private $firebase;
    private $email;
    public function __construct()
    {
        $this->firebase = new FirebaseController;
        $this->email = new EmailController;
        $this->notificationController = new NotificationController;
    }
    public function getFavour()
    {
        $data = FavoriteUser::where('userid', Auth::user()->id)->get();
        foreach ($data as $key => $value) {
            # code...
            $value->user;
            $value->favour_user;
        }
        return response()->json([ 'success'=>true, 'data' => $data ], 200);
    }
    public function favour(Request $request)
    {
        $userid = $request->userid;
        $data = ['userid' => Auth::user()->id, 'favour_userid' => $userid];
        if(FavoriteUser::where($data)->count() > 0){
            return response()->json([ 'success' => false, 'code' => 1 ], 200);
        }
        FavoriteUser::create($data);
        return response()->json([ 'success'=>true, 'code' => 0 ], 200);
    }

    public function connectServer(Request $request) {
        Connect::updateOrInsert(
            ['user_id' => $request->id],
            [
                'user_id' => $request->id,
                'created_at' => now(),
                'updated_at' => now()
            ]
        );
        return response()->json(['success'=>true], 200);
    }

    public function increaseTime(Request $request) {
        $date  = now();
        $formatted_date = $date->format('Y-m-d H:i:s');
        auth()->user()->update(['role' => 3, 'dt_premium' => $formatted_date]);

        $user = Auth::user();
        $success['token'] =  $user->createToken($user->id)->accessToken; 
        $success['user'] =  $user;
        $user->userRole;
        return response()->json(['success'=>true, 'data'=>$success], 200);
    }

    public function getRole(Request $request) {
        $role = User::where('id', $request->id)->get('role');
        return response()->json(['success'=>true, 'data' => $role], 200);
    }

    public function refreshServe(){
        $date  = now();
        $date->modify('-420 minutes');
        $premium_date = $date->format('Y-m-d H:i:s');
        $date  = now();
        $date->modify('-60 minutes');
        $general_date = $date->format('Y-m-d H:i:s');
        // foreach ($articles as $key => $value) {
        $articles = Article::where('active', '=' , 1)->where('state', '=', 1)->orderByDesc('userId')->get();
        for($i = 0; $i < count($articles); $i ++){
            $role = User::where('id', $articles[$i]->userId)->get('role')[0]->role;
                # code...
            if(($articles[$i]->updated_at < $general_date) && $role == 2){
                $articles[$i]->where('id', '=' ,$articles[$i]->id)->update(['active'=> 0]);
                $token = User::where('id', $articles[$i]->userId)->get('device_token')[0]->device_token;
                if($i > 0){
                    if(($articles[$i]->userId == $articles[$i - 1]->userId) && ($articles[$i - 1]->updated_at < $general_date)){
                        continue;
                    }
                }
                $title = "";
                $content = "";
                $title = "Warning";
                $content = "Your offer has just been offline, automatically repost the offer in the \"PREVIOUS OFFER PUBLISHED\" page.";
                if($token != null){
                    $this->notificationController->send2Android($title, $content, $token);
                }    
            }else if(($articles[$i]->updated_at < $premium_date) && $role == 3){
                $articles[$i]->where('id', '=' ,$articles[$i]->id)->update(['active'=> 0]);
                $token = User::where('id', $articles[$i]->userId)->get('device_token')[0]->device_token;
                if($i > 0){
                    if(($articles[$i]->userId == $articles[$i - 1]->userId) && ($articles[$i - 1]->updated_at < $premium_date)){
                        continue;
                    }
                }
                $title = "";
                $content = "";
                $title = "Warning";
                $content = "Your offer has just been offline, automatically repost the offer in the \"PREVIOUS OFFER PUBLISHED\" page.";
                if($token != null){
                    $this->notificationController->send2Android($title, $content, $token);
                }   
            }
        }
    }

    public function checkLimitOffer(){
        $allUser = User::where('role', '=', 2)->get();
        for($i = 0; $i < count($allUser); $i ++){
            $articles = Article::where('userId', '=', $allUser[$i]->id)->where('state', '=', 1)->get();
            $count = count($articles);
            if($count > 3){
                $token = $allUser[$i]->device_token;
                $title = "";
                $content = "";
                $title = "Warning";
                $content = "Your saved offer limit has been reached, avoid this limit with a premium account.";
                if($token != null){
                    $this->notificationController->send2Android($title, $content, $token);
                }  
            }
        }
    }

    public function checkCountry(Request $request){
        $date  = now();
        $date->modify('-24 hours');
        $dtUpdate = $date->format('Y-m-d H:i:s');
        $state = User::where('id', '=', $request->id)->where('updated_at', '>', $dtUpdate)->get('change_country')[0]->change_country;
        return response()->json(['success'=>true, 'state' => $state], 200);
    }
}
