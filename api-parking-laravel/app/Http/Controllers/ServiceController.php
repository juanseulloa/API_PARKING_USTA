<?php

namespace App\Http\Controllers;

use App\Helpers\JwtAuth;
use App\Login;
use App\Rate;
use App\Service;
use App\User;
use Carbon\Carbon;
use Firebase\JWT\JWT;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use League\CommonMark\Block\Element\Document;
use mysql_xdevapi\Table;

class ServiceController extends Controller
{
    /**
     * ServiceController constructor.
     */
    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    /**
     * listado de servicios generados
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        $service = Service::all()->load('users')->load('login')->load('rate');
        return response()->json($service, 200);

    }

    public function show($id)
    {

        $service = Service::all()->where('ID_SERVICE', $id)->first();
        if ($service == null)
            return response()->json('Servicio No Generado', 400);

        return response()->json($service, 200);
    }

    public function find($document)
    {
        $service = DB::select("SELECT v.LICENSE_PLATE, v.TIPE_VEHICLE, u.DOCUMENT , s.PRICE
      FROM users u INNER JOIN vehicle v INNER JOIN service s ON
      u.ID_USER=v.ID_USER && u.ID_USER=s.ID_USER WHERE u.DOCUMENT='$document'");
        if (is_object($service)) {
            $data = [
                'code' => 200,
                'status' => 'succes',
                'service' => $service
            ];

        } else {
            $data = [
                'code' => 400,
                'status' => 'Error',
                'message' => 'el servicio no existe'
            ];

        }
        return response()->json($data, $data['code']);

    }


    public function create(Request $request)
    {
        $jwtAuth = new JwtAuth();
        $validate = \Validator::make($request->all(), [
            'licence_plate' => 'required|max:6',
            'document' => 'required|max:11|unique:users',
            'tipe_rate' => 'required',
        ]);
        // if ($validate->fails())
        //     return response()->json('Servicio No creado', 400);
        $user = $this->createUser($request);
        $request->document = $user->DOCUMENT;
        $token = $request->header('Authorization');
        $tmp = $jwtAuth->token($token);
        $service = $this->createService($request, $tmp->sub);
        return response()->json($service, 200);

    }


    public function update($id, Request $request)
    {
        $service = Service::all()->where('ID_SERVICE', $id)->first();
        if ($service == null)
            return response()->json('Servicio No Generado', 400);
        $validate = \Validator::make($request->all(), [
            'license_plate' => 'required|max:6|min:6|',
        ]);
        if ($validate->fails())

            unset($request->ID_SERVICE);
        $service->LICENSE_PLATE = $request->license_plate;
        $service->update();

        return response()->json($service, 200);


    }

    public function createUser(Request $request)
    {
        $user = User::where('DOCUMENT', $request->document)->first();
        if ($user == null) {

            $user = new User();
            $user->DOCUMENT = $request->document;
            $user->NAME = ' ';
            $user->ROL_USER = 'User';
            $user->EMAIL = ' ';
            $user->TELEPHONE = ' ';
            $user->STATE_USER = 1;
            $user->save();
        }
        return $user;
    }


    /**
     *
     * metodo para validar servicios y guardar
     * @param $request
     * @param $idAd codigo del Admin
     * @return Service
     * @throws \Exception
     */
    public function createService($request, $idAd)
    {
        $date = new Carbon();

        $user = User::where('DOCUMENT', $request->document)->first();
        $service = new Service();
        $service->ID_LOGIN = $idAd;
        $service->ID_USER = $user->ID_USER;
        $service->START_DATE = $date->toDateString();
        $service->LICENSE_PLATE = $request->license_plate;

        if ($user->ROL_USER == 'User') {
            $request->tipe_rate = 'DAY_' . $request->tipe_rate;
            $rate = Rate::where('TIPE_RATE', '=', $request->tipe_rate)->first();
            $service->END_DATE = $date->toDateString();
            $service->ID_RATE = $rate->ID_RATE;
            $service->save();
        } else {
            $request->tipe_rate = 'MONT_' . $request->tipe_rate;
            $rate = Rate::where('TIPE_RATE', '=', $request->tipe_rate)->first();
            $service->END_DATE = $date->lastOfMonth()->toDateString();
            $service->ID_RATE = $rate->ID_RATE;
            $service->save();

        }
        return $service;
    }

    /**
     * @param $document
     * @return \Illuminate\Http\JsonResponse donde se obtiene los datos de los tiquets
     * @throws \Exception
     */

    public function getTicket($document)
    {
        $date = new Carbon();
        $date->toDateString();
        $month = $date->month;

        $user = User::where('DOCUMENT', '=', $document)->first();
        if ($user->ROL_USER == 'User') {
            $ticket = DB::table('users as u')->join('service as s', 'u.ID_USER', '=', 's.ID_USER')
                ->join('rate as r', 's.ID_RATE', '=', 'r.ID_RATE')->where('u.document', '=', $document)
                ->whereDate('s.START_DATE', $date)->select('s.ID_SERVICE', 'u.DOCUMENT', 'S.LICENSE_PLATE', 'r.PRICE', 's.START_DATE', 'r.TIPE_RATE')
                ->first();
        } else {
            $ticket = DB::table('users as u')->join('service as s', 'u.ID_USER', '=', 's.ID_USER')
                ->join('rate as r', 's.ID_RATE', '=', 'r.ID_RATE')->where('u.document', '=', $document)
                ->whereMonth('s.START_DATE', $month)->select('s.ID_SERVICE', 'u.DOCUMENT', 'S.LICENSE_PLATE', 'r.PRICE', 's.START_DATE', 'r.TIPE_RATE')
                ->first();
        }
        if ($ticket == null)
            return response()->json('Servicio No Generado', 400);

        return response()->json($ticket, 200);
    }

    /**
     * @param $request
     * @return JsonResponse
     * @throws /Exception
     */
    public function reportByMonth($month)
    {
        $date = new Carbon();
        if ($month > $date->month)
            return response()->json('Reporte No Generado', 400);

        $report = DB::table('users as u')->
        join('service as s', 'u.ID_USER', '=', 's.ID_USER')
            ->join('rate as r', 's.ID_RATE', '=', 'r.ID_RATE')
            ->whereMonth('s.START_DATE', $month)
            ->select('s.ID_LOGIN', 's.ID_SERVICE', 'u.DOCUMENT', 'S.LICENSE_PLATE', 'r.PRICE', 's.START_DATE', 's.END_DATE', 'r.TIPE_RATE')->get();


        return response()->json($report, 200);

    }

    /**
     * @param $rate
     * @return JsonResponse
     * @throws \Exception
     */

    public function reportByRate($rate)
    {
        $date = new Carbon();
        $month = $date->month;
        $report = DB::table('users as u')
            ->join('service as s', 'u.ID_USER', '=', 's.ID_USER')
            ->join('rate as r', 's.ID_RATE', '=', 'r.ID_RATE')
            ->where('r.TIPE_RATE', '=', $rate)
            ->whereMonth('s.START_DATE', $month)
            ->select('s.ID_LOGIN', 's.ID_SERVICE', 'u.DOCUMENT', 'S.LICENSE_PLATE', 'r.PRICE', 's.START_DATE', 's.END_DATE', 'r.TIPE_RATE')->get();
        if ($report == null)
            return response()->json('Reporte No Generado', 400);

        return response()->json($report, 200);

    }

    /**
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */

    public function reportUser()
    {
        $date = new Carbon();
        $month = $date->month;
        $users = DB::table('users as u')
            ->join('service as s', 'u.ID_USER', '=', 's.ID_USER')
            ->where('u.ROL_USER', '=', 'Special')
            ->whereMonth('s.START_DATE', $month)
            ->select('u.DOCUMENT', 'u.NAME', 'u.EMAIL', 'u.TELEPHONE', 'S.LICENSE_PLATE')->get();
        $user = DB::table('users as u')
            ->join('service as s', 'u.ID_USER', '=', 's.ID_USER')
            ->where('u.ROL_USER', '=', 'Special')
            ->whereMonth('s.START_DATE', $month - 1)
            ->select('u.DOCUMENT', 'u.NAME', 'u.EMAIL', 'u.TELEPHONE', 'S.LICENSE_PLATE')->get();


        var_dump($users);

        return response()->json($user, 200);


    }

}
