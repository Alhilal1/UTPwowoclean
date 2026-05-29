<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Container;
use App\Models\TrackingLog;

use Illuminate\Support\Facades\Auth;

class ContainerController extends Controller
{
    /**
 * @OA\Get(
 *     path="/api/v1/gateway/containers",
 *     summary="Get All Containers",
 *     tags={"Containers"},
 *
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\Response(
 *         response=200,
 *         description="Success"
 *     )
 * )
 */
    // GET ALL CONTAINERS
    public function index()
    {
        $containers = Container::with('logs')->get();

        return response()->json($containers);
    }

    /**
 * @OA\Post(
 *     path="/api/v1/gateway/containers",
 *     summary="Create Container",
 *     tags={"Containers"},
 *
 *     security={{"bearerAuth":{}}},
 *
 *     @OA\RequestBody(
 *         required=true,
 *
 *         @OA\JsonContent(
 *             required={"container_id","waste_type","weight_kg","status"},
 *
 *             @OA\Property(
 *                 property="container_id",
 *                 type="string",
 *                 example="GD12345"
 *             ),
 *
 *             @OA\Property(
 *                 property="waste_type",
 *                 type="string",
 *                 example="Chemical"
 *             ),
 *
 *             @OA\Property(
 *                 property="weight_kg",
 *                 type="integer",
 *                 example=500
 *             ),
 *
 *             @OA\Property(
 *                 property="status",
 *                 type="string",
 *                 example="Active"
 *             )
 *         )
 *     ),
 *
 *     @OA\Response(
 *         response=201,
 *         description="Container berhasil ditambahkan"
 *     ),
 *
 *     @OA\Response(
 *         response=403,
 *         description="Forbidden"
 *     )
 * )
 */
    // STORE CONTAINER
    public function store(Request $request)
    {
        // ROLE CHECK
        if (Auth::user()->role != 'admin') {

            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        // VALIDATION
        $validated = $request->validate([

            'container_id' => [
                'required',
                'unique:containers',
                'regex:/^[A-Z]{2}[0-9]{5}$/'
            ],

            'waste_type' => 'required',

            'weight_kg' =>
                'required|numeric|min:10|max:5000',

            'status' => 'required'

        ], [

            'container_id.required' =>
                'Container ID wajib diisi',

            'container_id.unique' =>
                'Container ID sudah digunakan',

            'container_id.regex' =>
                'Format ID harus 2 huruf + 5 angka',

            'waste_type.required' =>
                'Jenis limbah wajib diisi',

            'weight_kg.required' =>
                'Berat wajib diisi',

            'weight_kg.numeric' =>
                'Berat harus berupa angka',

            'weight_kg.min' =>
                'Minimal berat 10 kg',

            'weight_kg.max' =>
                'Maksimal berat 5000 kg',

            'status.required' =>
                'Status wajib diisi'
        ]);


        if (
            $request->waste_type == "Chemical"
            &&
            $request->weight_kg > 1000
        ) {

            return response()->json([
                "message" =>
                    "Limbah Chemical maksimal 1000 kg"
            ], 422);
        }

        // CREATE DATA
        $container = Container::create([

            'container_id' =>
                $request->container_id,

            'waste_type' =>
                $request->waste_type,

            'weight_kg' =>
                $request->weight_kg,

            'status' =>
                $request->status
        ]);

        return response()->json([

            "message" =>
                "Container berhasil ditambahkan",

            "data" => $container

        ], 201);
    }

    //SEARCH / FILTER
    public function search(Request $request)
    {
        $query = Container::query();


        if ($request->type) {

            $query->where(
                'waste_type',
                $request->type
            );
        }

        if ($request->min_weight) {

            $query->where(
                'weight_kg',
                '>=',
                $request->min_weight
            );
        }

        $containers = $query->get();

        return response()->json($containers);
    }

    // GET TRACKING LOGS
    public function logs($id)
    {
        $container = Container::with('logs')
            ->find($id);

        if (!$container) {

            return response()->json([
                "message" =>
                    "Container tidak ditemukan"
            ], 404);
        }

        return response()->json(
            $container->logs
        );
    }

    // ARCHIVE CONTAINER
    public function archive($id)
    {
        // ROLE CHECK
        if (Auth::user()->role != 'admin') {

            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $container = Container::find($id);

        if (!$container) {

            return response()->json([
                "message" =>
                    "Container tidak ditemukan"
            ], 404);
        }

        $container->status = "Archived";

        $container->save();

        return response()->json([

            "message" =>
                "Container berhasil diarchive",

            "data" => $container
        ]);
    }

    //DELETE CONTAINER
    public function destroy($id)
    {
        // ROLE CHECK
        if (Auth::user()->role != 'admin') {

            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $container = Container::find($id);

        if (!$container) {

            return response()->json([
                "message" =>
                    "Container tidak ditemukan"
            ], 404);
        }

        $container->delete();

        return response()->json([
            "message" =>
                "Container berhasil dihapus"
        ]);
    }
}