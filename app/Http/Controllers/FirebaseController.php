<?php

namespace App\Http\Controllers;
use Kreait\Firebase\Factory;
use Kreait\Firebase\ServiceAccount;
use Kreait\Firebase\Database;

use Illuminate\Http\Request;

class FirebaseController extends Controller
{
    //
    private $database;
    public function __construct()
    {
        $factory = (new Factory)->withServiceAccount(__DIR__.'/uvorem_firebase.json');
        $this->database = $factory->createDatabase();
    }
    public function ref($db){
        return $this->database->getReference($db);
    }
    public function get($db){
        return $this->ref($db)->getvalue();
    }
    public function set($db, $v){
        return $this->ref($db)->set($v);
    }
    public function deleteByValue($db, $value){
        $msgs = $this->ref($db)->get();
        foreach ($msgs as $key => $msg) {
            if($msg == $value){
                $this->delete($db.'/'.$key);
            }
        }
    }
    public function delete($db){
        return $this->ref($db)->remove();
    }
    public function push($db, $data){
        return $this->ref($db)->push($data)->getKey();
    }
    public function updateValue($db, $v, $isAdd){
        $befValue = $this->get($db);
        if($isAdd) $v = $befValue + $v;
        else $v = $befValue - $v;
        return $this->set($db, $v);
    }
    public function updateProfile($id){
        // General chat
        $db = "Messages/General";
        $data = $this->get($db);
        foreach ($data as $key => $value) {
            try {
                if($value['user']['_id'] != $id) continue;

                $avatar = explode("?", $value['user']['avatar']);
                $newAvatar = $avatar[0].'?'.time();
                $this->ref($db."/".$key."/user/avatar")->set($newAvatar);
            } catch (\Throwable $th) {
            }
        }

        $db = "Messages/Matches";
        $this->changeAvatar($db, $id);
        $db = "Messages/Private";
        $this->changeAvatar($db, $id);
        $db = "Messages/Team";
        $this->changeAvatar($db, $id);

    }
    public function changeAvatar($db, $userid){
        $data = $this->get($db);
        foreach ($data as $key => $value) {
            try {
                $data1 = $this->get($db."/".$key);
                foreach ($data1 as $key1 => $value1) {
                    if($value1['user']['_id'] != $userid) continue;
                    $avatar = explode("?", $value1['user']['avatar']);
                    $newAvatar = $avatar[0].'?'.time();
                    $this->ref($db."/".$key."/".$key1."/user/avatar")->set($newAvatar);
                }
            } catch (\Throwable $th) {
            }
        }
    }
}