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
use App\Http\Controllers\NotificationController;

use App\NotificationHistory;
use Illuminate\Validation\Rules\Exists;

class CountryController extends Controller
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
        $this->notificationController = new NotificationController;
    }
    
    public function index(Request $request)
    {
        $id = $request->id;
        $state = $request->state;
        if($id != 0){
            $change_country = User::where('id', $id)->get('change_country')[0]->change_country;
            
            if($change_country != $state){
                $new_country = User::where('id', $id)->get('new_country');
                $new_countryCode = User::where('id', $id)->get('new_countryCode');
                if($state == 0){
                    User::where('id', $id)->update(['change_country'=>0]);
                }else if($state == 1){
                    User::where('id', $id)->update(['change_country'=>1, 'country'=> $new_country[0]['new_country'], 'countryCode'=>$new_countryCode[0]['new_countryCode']]);
                }else if($state == 2){
                    User::where('id', $id)->update(['change_country'=>2]);
                }
                $token = User::where('id', $id)->get('device_token')[0]->device_token;
                $title = "";
                $content = "";
                $title = "Success";
                if($state == 0){
                    $content = "Your request to change the country of the account has been refused.";
                }else if($state == 1){
                    $content = "Your request to change the country of the account has been approved.";
                }
                if($token != null && $content != ""){
                    $this->notificationController->send2Android($title, $content, $token);
                }
            }
        }
        $user = User::where('change_country', 2)->get();
        return view('country.index', [
            'users' => $user
        ]);
    }

}
