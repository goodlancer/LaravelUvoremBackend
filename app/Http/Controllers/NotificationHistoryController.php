<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\NotificationHistory;
use App\User;
use Auth;
class NotificationHistoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $notification;
    private $firebase;
    public function __construct()
    {
        $this->notification = new NotificationController;
        $this->firebase = new FirebaseController;
    }
    public function index(NotificationHistory $model)
    {
        //
        return view('notification.index', [
            'users' => User::orderby('name')->where('role', '!=', 1)->get(),
            'data' => $model->orderby('created_at', 'desc')->get()
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $send_to = $request->send_to;
        $send_user_ids = $request->send_user_id;
        $send_group_id = $request->send_group_id;
        $title = $request->title;
        $content = $request->content;
        $users = null;
        $fileName = "";
        $file = $request->file('photo');
   
        if($file != null){
            $fileName = time().'.'.($file->extension());
            $path = '/uploads/notification/';
            if (!file_exists(public_path().$path)) {
                mkdir(public_path().$path, 0777, true);
            }
            $file->move(public_path().$path, $fileName);
            $fileName = $path.$fileName;
        }
        if($send_to == 0){
            $send_group_id = 0;
            if($send_user_ids && count($send_user_ids) > 0){ 
                if($send_user_ids[0] == '0')
                    $users = User::pluck('id')->toArray();
                else
                    $users = User::whereIn('id', $send_user_ids)->pluck('id')->toArray();
            }
            else return back()->withStatus(__('Not selected users.'));
        }else{
            $send_user_ids = null;
            if($send_group_id > 0) 
                $users = User::where('groupid', $send_group_id)
                    ->orwhere('group1id', $send_group_id)
                    ->orwhere('group2id', $send_group_id)
                    ->orwhere('group3id', $send_group_id)
                    ->orwhere('group4id', $send_group_id)
                    ->pluck('id')->toArray();
            else return back()->withStatus(__('Not selected group.'));
        }
        $this->notification->send($title, $content, $users);

        foreach ($users as $key => $value) {
            $model = new NotificationHistory;
            $id = $model->create([
                'sender_id' => Auth::user()->id,
                'receiver_id' => $send_group_id > 0 ? 0 : $value,
                'group_id' => $send_group_id,
                'title' => $title,
                'content' => $content,
                'image' => $fileName,
                'type' => 0,
                'answer' => $request->answer ? 1 : 0,
            ])->id;
            $this->firebase->push('Notification/'.$value, $id);
            if($send_group_id > 0) break;
        }

        return back()->withStatus(__('Notification successfully sended.'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $id)
    {
        $model = new NotificationHistory;
        if($id == -1) $model->whereIn('id', $request->check)->delete();
        else if($id == 0) $model->truncate();
        else $model->where('id', $id)->delete();

        return back()->withStatus(__('Notification history successfully deleted.'));
    }
}
