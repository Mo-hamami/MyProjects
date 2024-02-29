<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('users.register');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $formFields = $request->validate(
            [
                'name' => ['required', 'min:3'],
                'email' => ['required', 'email', Rule::unique('users', 'email')],
                'password' => 'required|confirmed|min:8'
            ]);

            $formFields['password']= bcrypt($formFields['password']);

            $user = User::create($formFields);

            auth()->login($user);


        
            return redirect('/')->with('message', 'User Created And Logged-in Successfully!');
    }


    public function logout(Request $request)
    {
        auth()->logout();
        
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/')->with('message', 'You Have Been Logged-out!');
    }

    public function login()
    {
        return view('users.login');
    }

    public function authenticate(Request $request){
        $formFields = $request->validate(
            [
                'email' => ['required', 'email'],
                'password' => 'required'
            ]);

            if(auth()->attempt($formFields)){
                $request->session()->regenerate();
            return redirect('/')->with('message', 'You Have Been Logged-in!');
            }

            return back()->withErrors(['email' => 'Invalid Credentials'])->onlyInput('email');
    }

}