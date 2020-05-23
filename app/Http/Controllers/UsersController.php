<?php

namespace App\Http\Controllers;
use Auth;
use GuzzleHttp\Client;
use App\User;
use Illuminate\Support\Facades\Cookie;
use Laravel\Socialite\Facades\Socialite;
use Redirect;
use Validator;
use Exception;
use Illuminate\Http\Request;
use Laravel\Passport\Client as OClient;

class UsersController extends Controller
{
    public $successStatus = 200;

    public function login(Request $request) {
        if (Auth::attempt(['email' => request('email'), 'password' => request('password')])) {
            $oClient = OClient::where('password_client', 1)->first();


            $x = (array) $this->getTokenAndRefreshToken($oClient, array('email'=>request('email'), 'password' => request('password'), 'type'=>'password'));
            $x['original']['status'] = 'ok';
            if($request->has('redirect')){
                $request->session()->flash('token_data', $x['original']);
                return redirect($request->get('redirect'));
            }
            return response()->json($x['original'],200);
        }
        else {
            return response()->json(['error'=>'Unauthorised'], 401);
        }
    }
    public function redirectToProvider(Request $request)
    {
        Cookie::queue('redirect',$request->get('redirect'),30);
        //var_dump($request->get('redirect'));
        //die('x');
        return Socialite::driver('google')->redirect();
    }

    /**
     * Obtain the user information from GitHub.
     *
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function handleProviderCallback(Request $request)
    {
        $redirect = null;

        try {
            $user = Socialite::driver('google')->user();
        } catch (Exception $e) {
            //die('redirect');
            return Redirect::to(env('APP_URL').'/api/v1/login/google');
        }


        if($request->session()->has('redirect')){
            echo 'echo';
            $redirect = $request->session()->get('redirect');
        }

        $oClient = OClient::where('password_client', 1)->first();
        $x= (array) $this->getTokenAndRefreshToken($oClient,array('type'=>'social','access_token'=> $user->token, 'provider'=>'google'));
        $x['original']['status'] = 'ok';


        if($redirect){
            $request->session()->flash('token_data', $x['original']);
            return redirect($request->session()->get('redirect'));
        }else{
            return response()->json($x['original'],200);
        }


    }

    /**
     * Return user if exists; create and return if doesn't
     *
     * @param $githubUser
     * @return User
     */
    private function findOrCreateUser($oauth)
    {
        if ($authUser = User::where('oauth_id_google', $oauth->id)->first()) {
            return $authUser;
        }

        return User::create([
            'name' => $oauth->name,
            'email' => $oauth->email,
            'oauth_id_google' => $oauth->id,
            'avatar' => $oauth->avatar
        ]);
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        $password = $request->password;
        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = User::create($input);
        $oClient = OClient::where('password_client', 1)->first();
        return $this->getTokenAndRefreshToken($oClient, $user->email, $password);
    }

    public function refreshToken(Request $request) {
        $refresh_token = $request->header('Refreshtoken');
        $oClient = OClient::where('password_client', 1)->first();
        $http = new Client;

        try {
            $response = $http->request('POST', route('passport.token'), [
                'form_params' => [
                    'grant_type' => 'refresh_token',
                    'refresh_token' => $refresh_token,
                    'client_id' => $oClient->id,
                    'client_secret' => $oClient->secret,
                    'scope' => '*',
                ],
            ]);
            return json_decode((string) $response->getBody(), true);
        } catch (Exception $e) {
            return response()->json("unauthorized", 401);
        }
    }


    public function getTokenAndRefreshToken2(OClient $oClient, $code) {
        $oClient = OClient::where('password_client', 1)->first();
        $http = new Client;
        /*var_dump(array('form_params' => [
            'grant_type' => 'password',
            'client_id' => $oClient->id,
            'client_secret' => $oClient->secret,
            'username' => $email,
            'password' => $password,
            'scope' => '*',
        ]));*/
        $response = $http->request('POST', route('passport.token'), [
            'form_params' => [
                'grant_type' => 'authorization_code',
                'client_id' => 'client-id',
                'redirect_uri' => 'http://example.com/callback',
                'code_verifier' => $oClient->id,
                'code' => $code,
            ],
        ]);

        $result = json_decode((string) $response->getBody(), true);
        return response()->json($result, $this->successStatus);
    }
    public function getTokenAndRefreshToken(OClient $oClient, array $credential) {
        $oClient = OClient::where('password_client', 1)->first();
        $http = new Client;
        /*var_dump(array('form_params' => [
            'grant_type' => 'password',
            'client_id' => $oClient->id,
            'client_secret' => $oClient->secret,
            'username' => $email,
            'password' => $password,
            'scope' => '*',
        ]));*/
        $form_params = array();
        switch($credential['type']){
            case 'password':
                $form_params = array(
                    'grant_type' => 'password',
                    'client_id' => $oClient->id,
                    'client_secret' => $oClient->secret,
                    'username' => $credential['email'],
                    'password' => $credential['password'],
                    'scope' => '*',
                );
            break;
            case 'social':
                $form_params = array(
                    'grant_type' => 'social',
                    'client_id' => $oClient->id,
                    'client_secret' => $oClient->secret,
                    'provider' => $credential['provider'],
                    'access_token' => $credential['access_token'],
                    'scope' => '*',
                );
                break;
        };

        $response = $http->request('POST', route('passport.token'),['form_params' => $form_params]);

        $result = json_decode((string) $response->getBody(), true);
        return response()->json($result, $this->successStatus);
    }

    public function details() {
        $user = Auth::user();
        $permission = $user->getCombinedPermissions();

        $user['permission']  = $permission;
        $user['role']  = array('id'=> 'admin', 'name'=> "管理员", 'describe'=> "拥有所有权限",'status' => 1, 'creatorId'=> "system",
            "permissionList"=> array('dashboard'),
            'permissions' =>
            array(array(
            'roleId'=>'admin',
      'permissionId'=> 'dashboard',
      'permissionName'=> '仪表盘',
      'actions'=> '[{"action":"add","defaultCheck":false,"describe":"新增"},{"action":"query","defaultCheck":false,"describe":"查询"},{"action":"get","defaultCheck":false,"describe":"详情"},{"action":"update","defaultCheck":false,"describe":"修改"},{"action":"delete","defaultCheck":false,"describe":"删除"}]',
      'actionList'=> null,
      'dataAccess'=> null
        )
    ));

        $user['roleId']  = "admin";
        $user['avatar'] = 'https://gw.alipayobjects.com/zos/antfincdn/XAosXuNZyF/BiazfanxmamNRoxxVxka.png';

        return response()->json($user, $this->successStatus);
    }

    public function logout(Request $request) {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully logged out'
        ]);
    }

    public function unauthorized() {
        return response()->json("unauthorized", 401);
    }
}
