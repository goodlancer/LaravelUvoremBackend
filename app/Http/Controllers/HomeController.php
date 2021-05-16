<?php

namespace App\Http\Controllers;
use App\User;
use App\Article;
use App\Connect;
class HomeController extends Controller
{
    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        $date  = now();
        $date->modify('-1 minutes');
        $formatted_date = $date->format('Y-m-d H:i:s');
        $allUserCount = count(User::get());
        $requestUserCount = Article::where('state', '=', 0)->groupBy('userId')->count();
        $onlineUserCount = Connect::where('created_at', '>', $formatted_date)->count();
        return view('dashboard', ['allUserCount' => $allUserCount - 1, 'requestUserCount' => $requestUserCount, 'onlineUserCount' => $onlineUserCount]);
    }
    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        (new Contact)->where('id', $id)->delete();
        return back()->withStatus(__('Contact data successfully deleted.'));
    }
}
