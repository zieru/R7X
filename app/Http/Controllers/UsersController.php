<?php

namespace App\Http\Controllers;
use App;
use Auth;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\User;
use Hash;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Laravel\Socialite\Facades\Socialite;
use Redirect;
use Validator;
use Exception;
use Illuminate\Http\Request;
use Laravel\Passport\Client as OClient;use App\Components\User\Repositories\UserRepository;

class UsersController extends Controller
{
  private  $userRepository;
    public $successStatus = 200;
  public function __construct(UserRepository $userRepository)
  {
    $this->userRepository = $userRepository;
  }
    public function authCheck(Request $request)
    {
        $input = $request->all();

        $this->validate($request, [
            'username' => 'required',
            'password' => 'required',
        ]);

        $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (auth()->attempt(array($fieldType => $input['username'], 'password' => $input['password']),false,false)){
            $user = Auth::user();
            if($user->isActive()) {
                $user['user_group'] = $user_group = User::with('groups')->find($user->id)->groups->first();
                $user['user_group_id'] = $user_group->id;
                switch ($user_group->id) {
                    case 1:
                        $user_perm[] = 'admin';
                        break;
                    default:
                        $user_perm[] = 'user';
                }
                return response()->json($user, 200);
            }
        }else{
            return response()->json(['error'=>'Unauthorised'], 401);
        }

    }

    public function login(Request $request) {


        $fieldType = filter_var(request('email'), FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
        if (Auth::attempt([$fieldType => request('email'), 'password' => request('password'),'active' => 1])) {
            $user = Auth::user();
                $oClient = OClient::where('password_client', 1)->first();

                $x = (array) $this->getTokenAndRefreshToken($oClient, array('email'=>Auth::user()->email, 'password' => request('password'), 'type'=>'password'));
                $x['original']['status'] = 'ok';
                if($request->has('redirect')){
                    $request->session()->flash('token_data', $x['original']);
                    return redirect($request->get('redirect'));
                }
            return response()->json($x['original'],200);
        }
        else {
            return response()->json(['error'=>'Unauthorised, Please ask administrator to create your account or activate your account'], 401);
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
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);
        }

        echo 'pass';
        $password = $request->password;
        $input = $request->all();
        //$input['password'] = Hash::make($password);
        var_dump($input);
        $user = User::create($input);
        $user->groups()->attach($request->post('group'));
        $user->save();
        return $user;
        //$oClient = OClient::where('password_client', 1)->first();
        //return $this->getTokenAndRefreshToken($oClient, $user->email, $password);
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
  public function userlist(){

    $data = datatables()->of($this->userRepository->listUsers(request()->all())->toArray()['data']);
    return $data->toJson();
  }

    public function details() {
      $user_perm = array('dashboard','formrefund','formadj');
      $perm_combine = array();
        $user = Auth::user();
        $permission = $user->getCombinedPermissions();

        $user['user_group'] = $user_group = User::with('groups')->find($user->id)->groups->first();
        $user['user_group_id'] = $user_group->id;
          switch ($user_group->id){
            case 1:
              $user_perm[] = 'admin';
              break;
            Case 5;
                $user_perm[] = 'admin';
                $user_perm[] = 'itdev';
                break;
            default:
              $user_perm[] = 'user';
          }

          foreach ($user_perm as $x){
            $perm_combine[] = array(
              'roleId'=>'admin',
              'permissionId'=> $x,
              'permissionName'=> '仪表盘',
              'actions'=> '[{"action":"add","defaultCheck":false,"describe":"新增"},{"action":"query","defaultCheck":false,"describe":"查询"},{"action":"get","defaultCheck":false,"describe":"详情"},{"action":"update","defaultCheck":false,"describe":"修改"},{"action":"delete","defaultCheck":false,"describe":"删除"}]',
              'actionList'=> null,
              'dataAccess'=> null
            );
          }
        $user['group'] = '2x';
        $user['permission']  = $permission;
        $user['role']  = array('id'=> 'admin', 'name'=> "管理员", 'describe'=> "拥有所有权限",'status' => 1, 'creatorId'=> "system",
            "permissionList"=> $user_perm,
            'permissions' => $perm_combine
        );

        $user['roleId']  = "admin";
        //$user['avatar'] = 'https://gw.alipayobjects.com/zos/antfincdn/XAosXuNZyF/BiazfanxmamNRoxxVxka.png';
	    $user['avatar'] = '/avatar2.jpg';
	    $user['version'] = App::VERSION();
        $appDetail['version'] = App::VERSION();
        $appDetail['name'] = env('APP_NAME');
        $user['App'] = $appDetail;
        $file_size = 0;
        foreach( File::allFiles(storage_path()) as $file)
        {
            $file_size += $file->getSize();
        }
        $file_size = $file_size;
        $user['disk_capacity_mb']= 1000000;
        $user['disk_size_mb'] = $file_size;
        $user['disk_capacity_percent'] = ($user['disk_capacity_mb'] - $file_size)/$user['disk_capacity_mb'] ;
        $user['datetime'] =  Carbon::now()->setTimezone('Asia/Jakarta')->toDateTimeString();
        return response()->json($user, $this->successStatus);
    }
    public function datetime(){
        $user['datetime'] =  Carbon::now()->toDateTimeString();
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

    public function activateUser(){
      $stat = false;
      $return = array('success' => $stat,'active'=> null, 'name' => null);
      $user = User::find(request('user_id'));
      $user->active = request('active');
      $user->save();
      if($user->wasChanged('active')){
        $stat = true;
        $return = array('success' => $stat,'active'=> $user->active, 'name' => $user->name);
      }

      return datatables()->of(array($return))->toJson();
    }
}
