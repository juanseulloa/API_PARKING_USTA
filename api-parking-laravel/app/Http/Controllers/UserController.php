<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator as ValidatorAlias;
use Psy\Util\Json;
use Symfony\Component\Console\Input\Input;
use function GuzzleHttp\Promise\all;

class UserController extends Controller
{
    /**
     * UserController constructor.
     */
    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ["index", "show"]]);
    }

    /**
     * metodo para listar los usuarios
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {

        $users = User::where('STATE_USER','!=','null');
        return response()->json($users, 200);
    }

    /**
     * metodo para buscar usuario por $id
     * @param $id
     * @return JsonResponse
     */
    public function show($id)
    {
        $user = User::where('ID_USER', $id)->first();
        if ($user == null)
            return response()->json('Usuario No Valido', 200);

        return response()->json($user, '200');
    }

    /**
     * @param $document
     * @return \Illuminate\Http\JsonResponse
     */

    public function getByDocument($document)
    {
        $user = User::where('DOCUMENT',$document)->first();
        if ($user == null)
            return response()->json('Usuario No Valido', 400);
        if ($user->ROL_USER!='Special')
            return response()->json('Usuario Noo Valido', 401);

        return response()->json($user, '200');
    }

    /**
     * metodo para crear usuarios
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function create(Request $request)
    {
        $validate = \Validator::make($request->all(), [
            'document' => 'required|max:11|unique:users',

        ]);


        if ($validate->fails())
            return response()->json("Usuario no Creado", 400);

        $user = new User();
        $user->DOCUMENT = $request->document;
        $user->NAME = $request->name;
        $user->ROL_USER = 'Special';
        $user->EMAIL = $request->email;
        $user->TELEPHONE = $request->telephone;
        $user->STATE_USER=1;
        $user->save();
        return response()->json($user, 200);

    }

    /**
     *
     * metodo para actualizar usuario con el documento
     * @param $document
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public function update($document, Request $request)
    {
        $user = User::where('document', $request->document)->first();
        if ($user == null)
            return response()->json('Usuario No valido', 400);
        $validate = \Validator::make($request->all(), [
            'rol_user' => 'required|max:7'
        ]);
        unset($request->ID_USER);
        $user->ROL_USER = $request->rol_user;
        $user->NAME = $request->name;
        $user->EMAIL = $request->email;
        $user->TELEPHONE = $request->telephone;
        $user->update();
        return response()->json($user, 200);

    }

    public function delete($document)
    {
        $user = User::where('document', $document)->first();
        if ($user == null && $user->STATE_USER != null)
            return response()->json('Usuario No valido', 400);
        $user->STATE_USER = null;
        $user->update();
        return response()->json($user, 200);

    }

    public function users($rol_user)
    {
        $users = User::all()->where('ROL_USER', '=', $rol_user)->where('STATE_USER','=','1');;
        if ($users == null)
            return response()->json('No Existen Usuarios registrados', 400);
        return response()->json($users, 200);

    }


}
