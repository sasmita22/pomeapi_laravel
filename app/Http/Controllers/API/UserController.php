<?php



namespace App\Http\Controllers\API;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Staff;
use Illuminate\Support\Facades\Auth;
use Validator;

class UserController extends Controller
{

    public $successStatus = 200;

    public function login(){
        if(Auth::attempt(['email' => request('email'), 'password' => request('password')])){
            $user = Auth::user();
            $success['nip'] = $user->nip;
            $success['name'] = $user->name;
            $success['email'] = $user->email;            
            $success['token'] =  $user->createToken('nApp')->accessToken;
            $success['status'] = true;
            return response()->json($success, $this->successStatus);
        }
        else{
            return response()->json(['status'=>false], 401);
        }
    }

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'nip' => 'required|string|max:10|unique:staff',
            'name' => 'required|string',
            'email' => 'required|string|email|unique:staff',
            'password' => 'required',           
            'c_password' => 'required|same:password',
        ]);

        if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 401);            
        }

        $input = $request->all();
        $input['password'] = bcrypt($input['password']);
        $user = Staff::create($input);
        $success['token'] =  $user->createToken('nApp')->accessToken;
        $success['name'] =  $user->name;
        $success['leader'] =  $user->leader;

        return response()->json(['success'=>$success], $this->successStatus);
    }

    public function details()
    {
        $user = Auth::user();
        return response()->json(['success' => $user], $this->successStatus);
    }
}