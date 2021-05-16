<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use App\User;
use App\News;
use App\Matches;
use App\Notification;
use App\ChampionShip;
class NotificationController extends Controller
{
    //
    private $firebase;
    public function __construct(){
        $this->firebase = new FirebaseController; 
    }
    public function test(){
        return $this->send("Notification", "I'm testing notificaion now.", null);
    }
    public function send($title, $content, $users){
        $android_device_tokens = [];
        $iPhone_device_tokens = [];
        if($users){
            $android_device_tokens = User::whereIn('id', $users)->pluck('device_token')->toArray();
            $iPhone_device_tokens = User::whereIn('id', $users)->pluck('iphone_device_token')->toArray();
        }else{
            $android_device_tokens = User::pluck('device_token')->toArray();
            $iPhone_device_tokens = User::pluck('iphone_device_token')->toArray();
        }
        try {
            $this->send2Android($title, $content, $android_device_tokens);
        } catch (\Throwable $th) {
        }
        try {
            return $this->send2iPhone($title, $content, $iPhone_device_tokens);
        } catch (\Throwable $th) {
            return $th;
        }
        
    }
    public function send2Android($title, $content, $tokens){
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);
        $notificationBuilder = new PayloadNotificationBuilder($title);
        $notificationBuilder
            ->setBody($content)
            ->setSound('default')
            ->setColor('#ad0300')
            ->setIcon('ic_notification');

        $dataBuilder = new PayloadDataBuilder();

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        $responses = FCM::sendTo($tokens, $option, $notification, $data);
    }
    public function send2iPhone($title, $content, $tokens){
        foreach ($tokens as $key => $token) {
            if(!isset($token)) continue;
            $data = [
                'title' => $title,
                'content' => $content
            ];
            $this->firebase->push('PushNotification/'.$token, $data);
        }
    }
    public function sendNews($id){
        $model = new News;
        $data = $model->where("id", $id)->get()[0];
        $this->send("News", $data->title, null);
    }
    public function checkSet($score, $last){
        $score = explode("/", $score);
        if (count($score) != 2) return false;
        $limit = 25;
        if ($last) $limit = 15;
        if ($score[0] < $limit && $score[1] < $limit) return false;
        if (($score[0] == $limit || $score[1] == $limit) && abs($score[0] - $score[1]) < 2) return false;
        if (($score[0] > $limit || $score[1] > $limit) && abs($score[0] - $score[1]) != 2) return false;
        return true;
    }
    public function sendMatch($id, $insert){
        $model = new Matches;
        $data = $model->where("id", $id)->get()[0];
        $notify_flag = true;

        if($insert == false){
            $score_array = explode("-", $data->score);
            foreach ($score_array as $index => $score_item) {
                if($this->checkSet($score_item, ($index === (intval($data->type)+1)*2)) == false) {
                    $notify_flag = false;
                    break;
                }
            }
        }
        $model = new Notification;
        $notify = $model->where("active", 1)->orderby('order')->get();
        $content = "";
        foreach ($notify as $key => $value) {
            if(($key != 0 && $value->type != 5) || ($value->type == 5 && is_object($data->championShip))) $content.= " : ";

            if($value->type == 1) $content .= $data->teamA->name." vs ".$data->teamB->name;
            else if($value->type == 2) $content .= $data->short_score;
            else if($value->type == 3){
                if($data->live == 1) $content .="MATCH LIVE";
                else $content .="RESULT";
            }else if($value->type ==4) $content .= $data->score;
            else if($value->type == 5 && is_object($data->championShip)) $content .= $data->championShip->title;
        }

        if($notify_flag == true) $this->send("Match Result", $content, null);
    }
}
