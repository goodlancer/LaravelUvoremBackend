<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\SendMail;
use Illuminate\Support\Facades\Mail;
use App\User;
class EmailController extends Controller
{
    //
    public function sendBasicMail($email, $subject, $message) {
        Mail::to($email)->send(new SendMail($subject, $message));
    }
}
