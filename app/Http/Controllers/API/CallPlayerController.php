<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\NotificationHistory;
use App\CoachRole;
use App\Group;
use App\Http\Controllers\FirebaseController;
use Auth;
use App\Http\Controllers\NotificationController;

class CallPlayerController extends Controller
{
    //
    private $notification;
    private $firebase;
    public function __construct()
    {
        $this->notification = new NotificationController;
        $this->firebase = new FirebaseController;
    }
    public function getRole(CoachRole $model){
        $data = $model->where('coach_id', Auth::user()->id)->get('group_id');
        $res = [];
        foreach ($data as $key => $item) $res[] = $item->group;
        return response()->json([ 'success' => true, 'data' =>$res ], 200);
    }
    public function callPlayers(Request $request){
        $users = $request->users;
        $title = $request->notification['title'];
        $content = $request->notification['content'];
        $answer = $request->notification['answer'];
        $group_id = $request->group_id;
        $users = array_unique($users);
        $this->notification->send($title, $content, $users);
        foreach ($users as $key => $value) {
            $model = new NotificationHistory;
            $id = $model->create([
                'sender_id' => Auth::user()->id,
                'receiver_id' => $value,
                'title' => $title,
                'content' => $content,
                'answer' => $answer,
                'group_id' => $group_id,
                'type' => 0
            ])->id;
            $this->firebase->push('Notification/'.$value, $id);
        };
        return response()->json([ 'success' => true ], 200);
    }
    public function getNotifications(){
        $model = new NotificationHistory;
        $data = $model->where('receiver_id', Auth::user()->id)->orwhere('receiver_id', 0)->orderby('created_at', 'desc')->get();
        foreach ($data as $key => $value){
            $data[$key]->send_user;
            $data[$key]->group;
        }
        return response()->json([ 'success' => true, 'data' => $data ], 200);
    }
    public function sendedNotifications(){
        $model = new NotificationHistory;
        $data = $model->where('sender_id', Auth::user()->id)->orderby('created_at', 'desc')->get();
        foreach ($data as $key => $value){
            $data[$key]->receive_user;
            $data[$key]->group;
        }
        return response()->json([ 
            'success' => true,
            'data' => $data
        ], 200);
    }
    public function answerNotifications(Request $request){
        $model = new NotificationHistory;
        $model->where('id', $request->id)->update(['answer' => $request->answer]);
        return response()->json([ 'success' => true ], 200);
    }
    public function deleteNotification($id){
        NotificationHistory::where('id', $id)->delete();
        return response()->json([ 'success' => true ], 200);
    }
}
