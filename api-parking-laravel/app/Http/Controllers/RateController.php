<?php

namespace App\Http\Controllers;

use App\Rate;
use App\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Helpers\JwtAuth;
use Illuminate\Support\Facades\DB;

class RateController extends Controller
{
    public function __construct()
    {
        $this->middleware('api.auth', ['except' => ['index', 'show']]);
    }

    /**
     * @return arreglo de objetos de tipo Vehicle en Json
     */

    public function index()
    {
        $rate = Rate::all();
        return response()->json($rate, 200);
    }

    /**
     * @param $id del veiculo que se quiere buscar
     * @return arreglo de objetos en Json
     */
    public function show($id)
    {
        $rate = Rate::where('ID_RATE', $id)->first();
        if ($rate == null)
            return response()->json('No Existe Dicha tarifa', 400);

        return response()->json($rate, 200);

    }

    /**
     * metodo para la creacion de vehiculos
     *
     * @param Request $request
     * @return JsonResponse validacion en json
     *
     */


    public function create(Request $request)
    {
        $validate = \Validator::make($request->all(), [
            'tipe_rate' => 'required|min:5|max:10|unique:rate',
            'price' => 'required|max:11'
        ]);
        if ($validate->fails())
            return response()->json("Tarifa No Creada", 500);

        $rate = new Rate();
        $rate->TIPE_RATE = $request->tipe_rate;
        $rate->PRICE = $request->price;
        $rate->save();

        return response()->json($rate, 200);
    }

    /**
     * metodo para actualizar vehiculos
     * @param $id del veiculo que esta regisyrado y se desea actualizar
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    public
    function update($tipe_rate, Request $request)
    {
       $rate= Rate::where('TIPE_RATE',$tipe_rate)->first();
        if ($rate == null)
            return response()->json('Tarifa No Existe', 400);

        $validate = \Validator::make($request->all(), [
            'price' => 'required|max:11'
        ]);

        unset($request->ID_RATE);
        $rate->PRICE=$request->price;
        $rate->update();

        return \response()->json($rate, 200);
    }

}
