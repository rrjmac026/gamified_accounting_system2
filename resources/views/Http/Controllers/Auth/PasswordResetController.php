<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class PasswordResetController extends Controller
{
    public function show(Request $request, $token)
    {
        return view('auth.reset-password', [
            'request' => $request,
            'token' => $token
        ]);
    }
}
