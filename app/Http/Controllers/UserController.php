<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

use App\Models\User;



class UserController extends Controller
{
    public function login(){
        return view('authentication.login');
    } 

    public function login_proses(Request $request){


        $validator = Validator::make($request->all(), [
            'email' => ['required'],
            'password' => ['required'],
            
        ]);

        if ($validator->fails()) {
            return redirect()->route('login')
                        ->withErrors($validator)
                        ->withInput();
        }

        $data = [
            'email' => $request->email,
            'password' => $request->password
        ];

        if(Auth::attempt($data)){
            return redirect()->route('workspace.dashboard');
            $request->session()->regenerate();
        }else{
            return redirect()->route('login')->with('failed','Email atau Password Salah');
        }

    }

    public function register(){
        return view('authentication.register');
    }

    public function register_proses(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'fullname' => ['required'],
            'email' => ['required', 'email:dns', 'unique:users,email'],
            'password' => ['required', 'min:6'],
            'confirmPassword' => ['required', 'same:password'],
        ]);

        if ($validator->fails()) {
            // dd($validator);
            return redirect()->route('register')
                        ->withErrors($validator)
                        ->withInput();
        }

        $data['fullname']   = $request->fullname;
        $data['email']      = $request->email;
        $data['password']   = Hash::make($request->password);
        $data['profession'] = "notset";
        $data['experience_level'] = 0;
        $data['organization'] = "notset";
        $data['photo_profile'] = "notset";

        if(!$data){
            dd('error');
        }else{
            $result = User::create($data);
            if($result){
                return redirect()->route('login')->with('success','Register Success');
            }else{
                return redirect()->route('register')->with('failed','Register Failed');
            }
            
        }
    }
}