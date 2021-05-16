<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use App\ImageHistory;
use App\ArticleHistory;
use Auth;
use DB;
class OfferHistoryController extends Controller
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
    public function index(Request $request)
    {
        $data = DB::select('SELECT users.*, article_history.title, article_history.description, article_history.state, article_history.active as art_active, article_history.id as article_id, article_history.offerType FROM article_history 
        LEFT JOIN users ON users.id = article_history.userId WHERE users.role != 1 ORDER BY article_history.updated_at DESC');
        $arrImages = [];
        for($i = 0; $i < count($data); $i ++){
            $articleId = $data[$i]->article_id;
            $images = ImageHistory::where('articleId', $articleId)->get('imagePath');
            $arrImages[$i] = $images;
        }
        return view('offer_history.index', [
            'data' => $data,
            'images' => $arrImages
        ]);
    }
    public function destroy(Request $request, $id)
    {
        $model = new ArticleHistory;
        if($id == -1) $model->whereIn('id', $request->check)->delete();
        else if($id == 0) $model->truncate();
        else $model->where('id', $id)->delete();
        $model = new ImageHistory;
        if($id == -1) $model->whereIn('articleId', $request->check)->delete();
        else if($id == 0) $model->truncate();
        else $model->where('articleId', $id)->delete();
        return back()->withStatus(__('Notification history successfully deleted.'));
    }

}
