<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\http\Requests\Auth\AuthFilterRequest;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class AuthController extends Controller
{
    use AuthorizesRequests;

    public function login_show(AuthFilterRequest $request, User $use) : \Inertia\Response
    {
        return inertia('auth.login');
    }

    public function login_request(AuthFilterRequest $request) : \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => 'login_request']);
    }

    public function register_show(AuthFilterRequest $request, User $use) : \Inertia\Response
    {
        return inertia('auth.register');
    }

    public function register_request(AuthFilterRequest $request) : \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => 'register_request']);
    }

    public function confirm_password_show(AuthFilterRequest $request, User $use) : \Inertia\Response
    {
        return inertia('auth.confirm_password');
    }

    public function confirm_password_request(AuthFilterRequest $request) : \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => 'confirm_password_request']);
    }

    public function forget_password_show(AuthFilterRequest $request, User $use) : \Inertia\Response
    {
        return inertia('auth.forget_password');
    }

    public function forget_password_request(AuthFilterRequest $request) : \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => 'forget_password_request']);
    }

    public function reset_password_show(AuthFilterRequest $request, User $use) : \Inertia\Response
    {
        return inertia('auth.reset_password');
    }

    public function reset_password_request(AuthFilterRequest $request) : \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => 'reset_password_request']);
    }

    public function verify_email_show(AuthFilterRequest $request, User $use) : \Inertia\Response
    {
        return inertia('auth.verify_email');
    }

    public function verify_email_request(AuthFilterRequest $request) : \Illuminate\Http\JsonResponse
    {
        return response()->json(['message' => 'verify_email_request']);
    }
}
