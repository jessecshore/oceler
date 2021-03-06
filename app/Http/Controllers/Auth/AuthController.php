<?php

namespace oceler\Http\Controllers\Auth;

use oceler\User;
use Validator;
use oceler\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;

class AuthController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Registration & Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users, as well as the
    | authentication of existing users. By default, this controller uses
    | a simple trait to add these behaviors. Why don't you explore it?
    |
    */

    use AuthenticatesAndRegistersUsers, ThrottlesLogins;

    private $redirectTo = '/home/';
    protected $loginPath = '/login';
    protected $redirectAfterLogout = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest', ['except' => 'getLogout']);
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'name' => 'required|max:255',
            'email' => 'required|email|max:255|unique:users',
            'mturk_id' => 'required',
            'password' => 'required|confirmed|min:6',
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'mturk_id' => $data['mturk_id'],
            'password' => bcrypt($data['password']),
            'role_id' => 3, //Regular (non-admin) user
            'ip_address' => $_SERVER['REMOTE_ADDR'],
            'user_agent' => $_SERVER['HTTP_USER_AGENT'],
        ]);
    }

    protected function authenticated()
    {
      $user = User::find(\Auth::id());
      $user->ip_address = $_SERVER['REMOTE_ADDR'];
      $user->user_agent = $_SERVER['HTTP_USER_AGENT'];
      $user->save();
      return redirect()->intended('/home/');
    }
}
