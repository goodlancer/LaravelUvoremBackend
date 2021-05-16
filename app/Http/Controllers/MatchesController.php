<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Matches;
use App\Team;
use App\Group;
use App\ChampionShip;
use App\Notification;
use App\Http\Controllers\NotificationController;
class MatchesController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    private $notification;
    public function __construct()
    {
        $this->notification = new NotificationController;
    }
    public function index(Matches $matches)
    {

        return view('matches.index', [
            'type'=>'create',
            'edt_matches'=> null,
            'groups'=>(new Group)->orderby('isteam')->orderby('order')->orderby('name')->get(),
            'championships' => (new ChampionShip)->orderby('order')->get(),
            'matches' => $matches
                ->orderby('live', 'desc')
                ->orderby('date', 'desc')
                ->get()
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
    public function getScore($score) {
        $score = str_replace(' ', "", $score);
        $score = str_replace('_', "", $score);
        $score = str_replace('/-', "", $score);
        $score = str_replace('-/', "", $score);
        $end_ch = $score[strlen($score)-1];
        if($end_ch=='-' || $end_ch=='/' )
            $score = substr(0, strlen($score)-1);
        return $score;
    }

    public function store(Request $request, Matches $matches)
    {
        $match_type = $request->match_type == "checked" ? 0 : 1;
        $live_match = 0;
        if($request->live_match == "true")
            $live_match = 1;
        $match_id = $matches->create($request->merge(['live' => $live_match, 'type' => $match_type, 'score' => $this->getScore($request->score)])->all())->id;
        $this->notification->sendMatch($match_id, true);
        return redirect()->route('matches.index')->withStatus(__('Matches successfully created.'));
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
        $model = (new Matches)->where('id', $id)->get();
        return view('matches.index', [
            'type'=>'update',
            'groups'=>(new Group)->orderby('isteam')->orderby('order')->orderby('name')->get(),
            'championships' => (new ChampionShip)->orderby('order')->get(),
            'edt_matches'=> $model[0],
            'matches' => (new Matches)
                ->orderby('live', 'desc')
                ->orderby('date', 'desc')->get()]);
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
        (new Matches)->where('id', $id)->update([
            'teamA_id'=>$request->teamA_id,
            'teamB_id'=>$request->teamB_id,
            'score' => $this->getScore($request->score),
            'short_score'=>$request->short_score,
            'championid'=>$request->championid,
            'live'=>$request->live_match=="true" ? 1 : 0,
            'type'=>$request->match_type == "checked" ? 0 : 1,
            'date'=>$request->date]);
        $this->notification->sendMatch($id, false);
        return redirect()->route('matches.index')->withStatus(__('Matches successfully updated.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
        $model = new Matches;
        $model->where('id', $id)->delete();
        return back()->withStatus(__('Match successfully deleted.'));
    }
    public function getNotification(){
        return (new Notification)->orderby("order")->get();
    }
    public function saveNotification(Request $request){
        $type = $request->type;
        $data = $request->data;
        if($type == 2){
            $model = new Notification;
            $model->update(['order' => 0]);
            foreach ($data as $key => $value) {
                $model = new Notification;
                $model->where('id', $value)->update(['order' => ($key+1)]);
            }
        }else{
            $model = new Notification;
            $order = 0;
            if($type == 1) $order = $model->max("order");
            $model->where('id', $data)->update(['active' => $type, 'order' => $order]);
        }
    }
}
