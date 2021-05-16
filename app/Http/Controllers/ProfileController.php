<?php

namespace App\Http\Controllers;
use App\User;
use App\Http\Requests\ProfileRequest;
use App\Http\Requests\PasswordRequest;
use Illuminate\Support\Facades\Hash;
class ProfileController extends Controller
{
    /**
     * Show the form for editing the profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit(User $user)
    {
        return view('profile.edit', ['user' => $user]);
    }

    /**
     * Update the profile
     *
     * @param  \App\Http\Requests\ProfileRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(ProfileRequest $request, User $user)
    {
        $file = $request->file('photo_path');
        $path = "";
        if($file != null){
            $fileName = $user->id.'.'.($file->extension());
            $path = '/uploads/avatar/';
            if (!file_exists(public_path().$path)) {
                mkdir(public_path().$path, 0777, true);
            }
            $file->move(public_path().$path, $fileName);
            $path .= $fileName;
            $user->update(['avatar'=>$path]);
        }
        $user->update($request->all());
        return back()->withStatus(__('Profile successfully updated.'));
    }

    /**
     * Change the password
     *
     * @param  \App\Http\Requests\PasswordRequest  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function password(PasswordRequest $request, User $user)
    {
        $user->update(['password' => Hash::make($request->get('password'))]);

        return back()->withStatusPassword(__('Password successfully updated.'));
    }
}
