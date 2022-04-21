<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Validator;
use App\User;
use Hash;

class AuthController extends Controller
{
    public $token = true;
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login','register']]);
    }

    public function register(Request $request)
    {
 
        $validator = Validator::make($request->all(), [
            'firstname' => 'required|regex:/^[a-zA-Z]+$/u|max:255',
            'lastname' => 'required|regex:/^[a-zA-Z]+$/u|max:255',
            'mobile' => 'required|digits:10|unique:users',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
            'age' => 'required|numeric|min:10|max:100',
            'gender' => 'required|in:m,f,o',
            'city' => 'required|string|max:255',
        ]);

        if ($validator->fails()) {  
           return response()->json(['error'=>$validator->errors()], 500); 
        }   
 
        $user_details = User::create([
            'firstname' => $request->get('firstname'),
            'lastname' => $request->get('lastname'),
            'mobile' => $request->get('mobile'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'age' => $request->get('age'),
            'gender' => $request->get('gender'),
            'city' => $request->get('city')
        ]);
  
        if ($this->token) {
            return $this->login($request);
        }
  
        return response()->json([
            'success' => true,
            'user_details' => $user_details
        ], 201);
    }
    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (! $token = auth()->attempt($credentials)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json([
            'sucess'=>true,
            'user_details'=>$request->user()
        ],200);
    }

    public function destroy($id)
    {
        $user = User::find($id);
        if(empty($user)) {
            return response()->json([
                'success'=>false,
                'message'=>"User Not found in our system"
            ], 404);
        }

        $destroy_user = User::destroy($id);
        return response()->json([
                'success'=>true,
                'message'=>"User has been deleted successfully"
            ], 200);
    }

    public function index() {
        return response()->json([
            'success'=>true,
            'users'=>User::all()
        ]);
    }

    
}
