<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ContainerController extends Controller
{
    private $containers = [
        [
            "container_id" => "GD12345",
            "waste_type" => "Plastic",
            "weight_kg" => 200,
            "status" => "Active",
            "tracking_logs" => [
                ["location" => "Gudang A",  "timestamp" => "2026-04-16 10:00",  "description" => "Dikirim dari gudang"],
                ["location" => "Pelabuhan", "timestamp" => "2026-04-16 14:00",  "description" => "Dalam perjalanan"]
            ]
        ],
        [
            "container_id" => "SB54321",
            "waste_type" => "Chemical",
            "weight_kg" => 800,
            "status" => "Active",
            "tracking_logs" => [
                ["location" => "Gudang B", "timestamp" => "2026-04-15 09:00", "description" => "Siap dikirim"],
                ["location" => "Pabrik", "timestamp" => "2026-04-15 13:30", "description" => "Sedang diproses"]
            ]
        ],
        [
            "container_id" => "ML67890",
            "waste_type" => "Metal",
            "weight_kg" => 1200,
            "status" => "Archived",
            "tracking_logs" => [
                ["location" => "Gudang C", "timestamp" => "2026-04-14 08:00", "description" => "Diterima"],
                ["location" => "Tempat Daur Ulang", "timestamp" => "2026-04-14 16:00", "description" => "Selesai diproses"]
            ]
        ]
    ];

    public function index()
    {
        return response()->json($this->containers);
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'container_id' => ['required', 'regex:/^[A-Z]{2}[0-9]{5}$/'],
            'waste_type' => 'required',
            'weight_kg' => 'required|numeric|min:10|max:5000',
            'status' => 'required'
        ], [
            'container_id.required' => 'Container ID wajib diisi',
            'container_id.regex' => 'Format ID harus 2 huruf + 5 angka',

            'waste_type.required' => 'Jenis limbah wajib diisi',

            'weight_kg.required' => 'Berat wajib diisi',
            'weight_kg.numeric' => 'Berat harus berupa angka',
            'weight_kg.min' => 'Minimal berat 10 kg',
            'weight_kg.max' => 'Maksimal berat 5000 kg',

            'status.required' => 'Status wajib diisi'
        ]);

        // cek unique ID
        foreach ($this->containers as $c) {
            if ($c['container_id'] == $request->container_id) {
                return response()->json([
                    "error" => "Container ID sudah ada"
                ], 422);
            }
        }

        // validasi jika waste_type chemical (max weight 1000kg)
        if ($request->waste_type == "Chemical" && $request->weight_kg > 1000) {
            return response()->json([
                "error" => "Chemical maximum adalah 1000kg"
            ], 422);
        }

        return response()->json([
            "message" => "Berhasil ditambahkan"
        ], 201);
    }

    public function search(Request $request)
    {
        $data = collect($this->containers);

        if ($request->type) {
            $data = $data->where('waste_type', $request->type);
        }

        if ($request->min_weight) {
            $data = $data->where('weight_kg', '>=', $request->min_weight);
        }

        return response()->json($data->values());
    }

    public function logs($id)
    {
        foreach ($this->containers as $container) {
            if ($container['container_id'] == $id) {
                return response()->json($container['tracking_logs']);
            }
        }

        return response()->json(["message" => "Not found"], 404);
    }

    public function archive($id)
    {
        foreach ($this->containers as &$container) {
            if ($container['container_id'] == $id) {
                $container['status'] = "Archived";
                return response()->json($container);
            }
        }

        return response()->json(["message" => "Not found"], 404);
    }

    public function destroy($id)
    {
        foreach ($this->containers as $index => $container) {
            if ($container['container_id'] == $id) {
                array_splice($this->containers, $index, 1);
                return response()->json(["message" => "Deleted"]);
            }
        }

        return response()->json(["message" => "Not found"], 404);
    }
}
