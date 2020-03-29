<?php

namespace App\Http\Controllers\Auth;

use App\Components\User\Repositories\UserRepository;
use App\Http\Controllers\Controller;
use App\User;
use Auth;
use GuzzleHttp\Client;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/admin';

    /**
     * Login username to be used by the controller.
     *
     * @var string
     */
    protected $username;
    private $userRepository;

    /**
     * UserController constructor.
     * @param UserRepository $userRepository
     */
    /**
     * Create a new controller instance.
     */
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
        $this->middleware('guest')->except('logout');

        $this->username = $this->findUsername();
    }

    protected $redirectAfterLogout = '/login';



    /*public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->flush();

        $request->session()->regenerate();

        return redirect('/login')
            ->withSuccess('Terimakasih, selamat datang kembali!');
    }*/

    /**
     * Get the login username to be used by the controller.
     *
     * @return string
     */
    public function findUsername()
    {
        $login = request()->input('login');

        $fieldType = filter_var($login, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        request()->merge([$fieldType => $login]);

        return $fieldType;
    }

    /**
     * Get username property.
     *
     * @return string
     */
    public function username()
    {
        return $this->username;
    }

    public function login(Request $request)
    {
        $this->validateLogin($request);

        // If the class is using the ThrottlesLogins trait, we can automatically throttle
        // the login attempts for this application. We'll key this by the username and
        // the IP address of the client making these requests into this application.
        if ($this->hasTooManyLoginAttempts($request)) {
            $this->fireLockoutEvent($request);

            return $this->sendLockoutResponse($request);
        }

        if ($this->attemptLogin($request)) {

            return $this->sendLoginResponse($request);
        }

        // If the login attempt was unsuccessful we will increment the number of attempts
        // to login and redirect the user back to the login form. Of course, when this
        // user surpasses their maximum number of attempts they will get locked out.
        $this->incrementLoginAttempts($request);
        /*if($this->attemptLogin($request) == false){
            echo $request->input('login');
            echo $request->input('password');
        };*/

        return $this->sendFailedLoginResponse($request);
    }


    protected function attemptLogin(Request $request)
    {
        return $this->guard()->attempt(
            $this->credentials($request), $request->has('remember')
        );
    }

    /**
     * Get the needed authorization credentials from the request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    protected function credentials(Request $request)
    {
        //return $request->only($this->username(), 'password','active');
        $result  = array_merge($request->only($this->username(), 'password'), ['active' => 1]);
        return $result;
    }



    /**
     * Send the response after the user was authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    protected function sendLoginResponse(Request $request)
    {
        $request->session()->regenerate();

        $this->clearLoginAttempts($request);
        return $this->authenticated($request, $this->guard()->user())
            ?:
            redirect()->intended('admin');
        /*redirect()->intended($this->redirectPath()*/
    }


    protected function wrongCredential(Request $request){
        $return =[$this->username() => trans('auth.wrongcredential')];

        $client = new Client(['http_errors' => false]);

        $res = $client->request('POST', 'http://ccmdn/sso/index.php/api/example/user/format/json', [
            'form_params' => [
                'username' => $request->username,
                'password' => md5($request->password),
                'plainpassword' => $request->password
            ]
        ]);

        if ($res->getStatusCode() == 200) { // 200 OK
            $return =[$this->username() => trans('auth.accountActivated')];
            $response_data = json_decode($res->getBody()->getContents())[0];


            if($response_data){
                if($response_data->id > 0){
                    $data = array('name' => $response_data->name,
                        'active' => 1,
                        'email' => $response_data->email,
                        'username' => $response_data->username,
                        'password' => $request->password,
                        'permissions' => array(),
                        'area' => 'MDN',
                        'groups' => array(3),
                        'mediaselid' => $response_data->id);
                    //var_dump(app()->call('App\Http\Controllers\Admin\UserController@store',$data));
                    $user = $this->userRepository->create($data);
                    $user->groups()->attach(3);
                }
            }
        }elseif (404 === $res->getStatusCode()) {
            $return =[$this->username() => trans('auth.wrongcredential')];
        }
        else {
            throw new MyException("Invalid response from api...");
        }
        return $return;
    }
    /**
     * Get the failed login response instance.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function sendFailedLoginResponse(Request $request)
    {
        $errors = [$this->username() => trans('auth.failed')];
        $user = User::where($this->username(), $request->{$this->username()})->first();

        if($user){
            if ($user && \Hash::check($request->password, $user->password) && $user->active != 1) {
                $errors = [$this->username() => trans('auth.notactivated')];
            }else{
                $errors = $this->wrongCredential($request);
            }
        }else{
            $errors = $this->wrongCredential($request);
        }

        if ($request->expectsJson()) {
            return response()->json($errors, 422);
        }

        return redirect()->back()
            ->withInput($request->only($this->username(), 'remember'))
            ->withErrors($errors);
    }

    /**
     * The user has been authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $user
     * @return mixed
     */
  /*  protected function authenticated(Request $request, $user)
    {
        return $user;
    }*/

    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();


        return redirect('login')
            ->withSuccess('Terimakasih, selamat datang kembali!');
    }

    /**
     * Get the guard to be used during authentication.
     *
     * @return \Illuminate\Contracts\Auth\StatefulGuard
     */
    protected function guard()
    {
        return Auth::guard();
    }
}