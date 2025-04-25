<?php

namespace App\Http\Controllers;

use App\Http\Requests\CodeConfirmationRequest;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\StoreLeadRequest;
use App\Http\Requests\StoreUserRequest;
use App\Http\Resources\UserResource;
use App\Mail\CodeMail;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Mail\Mailable;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    public function signup(StoreUserRequest $request)
    {
        $user = User::create($request->validated());
        $user->token = $user->createToken('auth_token')->plainTextToken;
        return UserResource::make($user);
    }

    public function signin(LoginUserRequest $request)
    {
        Auth::attempt($request->only('email', 'password'));
        $user = User::where('email', $request->email)->first();
        $user->token = $user->createToken('auth_token')->plainTextToken;
        return UserResource::make($user);
    }


    public function checkUser()
    {

    }
    public function telegramData(StoreUserRequest $request)
    {

        $code = Str::random(6);
        $data = $request->validated();
        Log::info($data);
        $data['code'] = Hash::make($code);
        if(Cache::has('user:' . $request->email . ':telagramdata')) {
            Cache::forget('user:' . $request->email . ':telagramdata');
        }
        Cache::add('user:' . $request->email .  ':telagramdata', $data, 300  );
        try {
            Mail::to($request->email)->send(new CodeMail($request->name, $code));
        } catch (\Throwable $th) {
            return response()->json(['status' => 'error', 'message' => $th->getMessage()]);
        }
        return response()->json(['status' => 'success', 'message' => 'Код отправлен']);
    }

    public function userConfirmation(CodeConfirmationRequest $request)
    {

        if(Cache::has('user:' . $request->email . ':telagramdata')){
            $data = Cache::get('user:' . $request->email . ':telagramdata');

            if (Hash::check($request->code, $data['code'])) {
                unset($data['code']);
                $user = User::create($data);
                $user->token = $user->createToken('auth_token')->plainTextToken;
                return UserResource::make($user);
            }
            else {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Код неверный'
                ]);
            }
        }
        else {
            return response()->json([
                'status' => 'error',
                'message' => 'Не найден пользователь'
            ]);
        }
    }
}
