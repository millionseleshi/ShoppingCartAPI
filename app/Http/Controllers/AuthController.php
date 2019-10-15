<?php

namespace App\Http\Controllers;


use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        $validator=Validator::make($request->all(),
            [
                'rememberMe' => 'boolean'
            ]);
        if ($validator->fails())
        {
            return response()->json($validator->errors());
        }
        else
        {
            $credentials = request(['username', 'password']);
            if(Auth::attempt($credentials))
            {
                $user = Auth::user();
                $tokenResult = $user->createToken('UserToken');
                $token = $tokenResult->token;
                if ($request->rememberMe) {
                    $token->expires_at = Carbon::now()->addWeeks(1);
                    $token->save();
                }
                $success['userId']=Auth::user()->id;
                $success['userName']=Auth::user()->userName;
                $success['message']='Successfully Logged in as '.$request->input('username');
                $success['tokenType']='Bearer ';
                $success['token']=$tokenResult->accessToken;
                //$success['expiresAt']= Carbon::parse($tokenResult->token->expires_at)->toDateTimeString();
                return response()->json($success);
            }

            return response()->json(['message' => 'Unauthorized']);

        }
    }

    public function register(Request $request)
    {
        $validator=Validator::make($request->all(),[
            'firstName' => ['required', 'string', 'max:20','min:2'],
            'lastName' => ['required', 'string', 'max:20','min:2'],
            'username'=>['required','string','max:15','min:2','unique:users'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);

        if($validator->fails())
        {
            return response()->json($validator->errors());
        }
        else
        {
            $user = User::create(
                [
                    'firstName' => $request['firstName'],
                    'lastName' => $request['lastName'],
                    'userName' => $request['username'],
                    'email' => $request['email'],
                    'password' => bcrypt($request['password'])
                ]);

            $success['userName'] = $request->username;
            $success['token'] =  $user->createToken('UserToken')-> accessToken;
            $success['tokenType'] = "Bearer ";
            $success['message'] =  "Successfully Created Account";
            return response()->json($success, 200);
        }
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();
        return response()->json([
            'message' => 'Successfully Logged Out'
        ]);
    }

    public function user(Request $request)
    {
        return response()->json($request->user(),200);
    }

    public function editUser(Request $request)
    {
        $userID=Auth::user()->id;
        $validator=Validator::make($request->all(),[
            'firstName' => ['required', 'string', 'max:20','min:2'],
            'lastName' => ['required', 'string', 'max:20','min:2'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
        if($validator->fails())
        {
            return response()->json($validator->errors());
        }
        else
        {
            $user=User::find($userID);
            $user->firstName=$request['firstName'];
            $user->lastName=$request['lastName'];
            $user->email=$request['email'];
            $user->save();
            return response()->json('Profile edited Successfully',200);
        }
    }
}
