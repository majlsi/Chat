<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Helpers\SecurityHelper;
use Helpers\UserHelper;
use Illuminate\Http\Request;
use Models\User;
use Services\SocialService;
use Services\UserService;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthenticateController extends Controller
{

    private $userService;
    private $socialService;
    private $userHelper;
    private $securityHelper;

    public function __construct(UserService $userService, SocialService $socialService, UserHelper $userHelper, SecurityHelper $securityHelper)
    {
        $this->userService = $userService;
        $this->socialService = $socialService;
        $this->userHelper = $userHelper;
        $this->securityHelper = $securityHelper;
    }

    public function index()
    {
        // TODO: show users
    }

    public function register(Request $request)
    {

        $data = $request->all();

        $userData = $this->userHelper->prepareUserDataOnCreate($data, config('providers.custom'));

        $validator = Validator::make($userData, User::rules('save'));
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()->all()], 400);
        }
        $existUser = $this->userService->login($userData['username'], $userData['app_id']);
        if (!$existUser) {
            $created   = $this->userService->create($userData);
            if ($created) {
                return response(["created" => $created], 200);
            }
        } else {
            return response(["created" => $existUser], 200);
        }

    }

    /**
     * Login
     * @param Request $request
     * @return Token
     */
    public function authenticate(Request $request)
    {
        $credentials = $request->only('username', 'app_id');
        try {
            // verify the credentials and create a token for the user
            $user = $this->userService->login($credentials['username'], $credentials['app_id']);

            if (!$user) {
                return response()->json(['error' => 'invalid_credentials'], 401);
            }
        } catch (JWTException $e) {
            // something went wrong
            return response()->json(['error' => 'could_not_create_token'], 500);
        }

        $customClaims = ['user_id' => $user->id];
        $token = JWTAuth::fromUser($user, $customClaims);
        // if no errors are encountered we can return a JWT
        $is_verified = $user->is_verified;
        $roles = [$user->role->role_name];
        return response()->json(compact('token', 'is_verified', 'roles'), 200);
    }

    /**
     * Get Current Authenticated User
     *
     * @return User
     */
    public function getAuthenticatedUser()
    {

        try {
            $payload = JWTAuth::parseToken()->getPayload();
            if (!$payload->get('user_id')) {
                return response()->json(['user_not_found'], 404);
            }
        } catch (Tymon\JWTAuth\Exceptions\TokenExpiredException $e) {

            return response()->json(['token_expired'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\TokenInvalidException $e) {

            return response()->json(['token_invalid'], $e->getStatusCode());
        } catch (Tymon\JWTAuth\Exceptions\JWTException $e) {

            return response()->json(['token_absent'], $e->getStatusCode());
        }

        $user = $this->userService->getById($payload->get('user_id'));

        // if ($user->role_id == config('roles.client')) {
        //     $user->client;
        // }
        // the token is valid and we have found the user via the sub claim
        return response()->json(compact('user'), 200);
    }

    /**
     * Log Out
     */
    public function invalidate()
    {
        JWTAuth::invalidate(JWTAuth::getToken());
    }

    // public function verifyUser(Request $request)
    // {
    //     $verificationCode = $request->only('verification_code');

    //     if (!$verificationCode || $verificationCode["verification_code"] == '') {
    //         return response()->json(["error" => "Please Enter your Code"], 400);
    //     }

    //     $account = $this->securityHelper->getCurrentUser();

    //     if ($account->user->is_verified == 1) {
    //         return response()->json(["error" => "You have already verified your account"], 400);
    //     }

    //     if ($account->verification_code != $verificationCode["verification_code"] && $verificationCode["verification_code"] != "2018") {
    //         return response()->json(["error" => "Invalid Code"], 404);
    //     }

    //     $this->userService->update($account->user_id, ["is_verified" => 1]);

    //     return response()->json(['success' => "true"], 200);
    // }

    // public function resendVerificationCode(Request $request)
    // {
    //     $account = $this->securityHelper->getCurrentUser();

    //     if (!$account) {
    //         return response()->json(["error" => "You are not registered at our system"], 400);
    //     }

    //     if ($account->user->is_verified == 1) {
    //         return response()->json(["error" => "You have already verified your account"], 400);
    //     }

    //     $verificationCode = $account->verification_code;

    //     //todo send sms with the code

    //     return response()->json(['code' => $verificationCode], 200);
    // }

    public function socialLogin(Request $request)
    {
        $data = $request->all();
        $provider = $data["provider"];
        $accessToken = $request->social_access_token;
        $googleAuthCode = $request->google_auth_code;
      
        $socialUser = $this->socialService->getUserFromToken($provider, $accessToken, $googleAuthCode);
       
        if ($socialUser != null) {          
            $user = $this->socialService->handleSocialUser($socialUser, $provider);
        } else {
            return response()->json(['error' => "Not Valid"], 400);
        }

        //login here
        $customClaims = ['user_id' => $user->id];
        $token = JWTAuth::fromUser($user, $customClaims);
        $is_verified = $user->is_verified;
        $is_profile_completed = $user->username ? 1 : 0;
        return response()->json(compact('token', 'is_verified', 'is_profile_completed'), 200);
    }

    public function handleSocialCallback(Request $request, $provider)
    {
        $user = $this->socialService->socialLogin($provider);
        if (isset($user["error"])) {
            return response()->json(['error' => $user["error"]], 400);
        }
        //login here
        $customClaims = ['user_id' => $user->id];
        $token = JWTAuth::fromUser($user, $customClaims);
        $is_verified = $user->is_verified;
        $is_profile_completed = $user->username ? 1 : 0;
        return response()->json(compact('token', 'is_verified', 'is_profile_completed'), 200);
    }

}
