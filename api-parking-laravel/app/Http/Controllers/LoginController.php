<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\User;
use Illuminate\Foundation\ProviderRepository;
use Illuminate\Http\Request;

use App\Login;
use Illuminate\Support\Facades\DB;
use function GuzzleHttp\Promise\all;


class LoginController extends Controller
{

    /**
     * metodo para crear
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function create(Request $request)
    {
        $user = User::where('document', $request->document)->first();
        if ($user == null)
            return response()->json("usuario no existe", 500);

        $validatedData = $request->validate([
            'password' => 'required|unique:login',
        ]);
        $pwd = hash('sha256', $request->password);

        $login = new Login();
        $login->id_user = $user->ID_USER;
        $login->password = $pwd;
        $login->save();

        return response()->json("usuario con login", 200);


    }

    public function login(Request $request)
    {
        $jwtAuth = new JwtAuth();
        $user = User::where('document', $request->document)->first();
        if ($user == null)
            return response()->json("usuario no existe", 500);

        $pwd = hash('sha256', $request->password);
       // var_dump($request->all());
        $signup = $jwtAuth->signup($user->ID_USER, $pwd);

        if (!empty($request->gettoken)) {
            $signup = $jwtAuth->signup($user->ID_USER, $pwd, true);
        }
        
        return response()->json($signup, 200);
    }


}

