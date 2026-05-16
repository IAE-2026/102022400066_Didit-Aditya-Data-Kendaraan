<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use Illuminate\Support\Facades\Validator;
use OpenApi\Attributes as OA;

class VehicleController extends Controller
{
    private function successResponse($data, $message = 'Data retrieved successfully', $status = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'meta' => [
                'service_name' => 'Vehicles-Service',
                'api_version' => 'v1'
            ]
        ], $status);
    }

    private function errorResponse($message, $errors = null, $status = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $status);
    }

    #[OA\Get(path: "/api/v1/vehicles", summary: "Mengambil daftar seluruh armada", security: [["ApiKeyAuth" => []]])]
    #[OA\Response(response: 200, description: "Success")]
    public function index()
    {
        $vehicles = Vehicle::all();
        return $this->successResponse($vehicles);
    }

    #[OA\Get(path: "/api/v1/vehicles/{id}", summary: "Mengambil data spesifik kendaraan", security: [["ApiKeyAuth" => []]])]
    #[OA\Parameter(name: "id", in: "path", required: true, schema: new OA\Schema(type: "integer"))]
    #[OA\Response(response: 200, description: "Success")]
    #[OA\Response(response: 404, description: "Not Found")]
    public function show($id)
    {
        $vehicle = Vehicle::find($id);
        if (!$vehicle) {
            return $this->errorResponse('Resource not found', null, 404);
        }
        return $this->successResponse($vehicle);
    }

    #[OA\Post(path: "/api/v1/vehicles", summary: "Menambah data master kendaraan baru", security: [["ApiKeyAuth" => []]])]
    #[OA\RequestBody(
        required: true,
        content: new OA\JsonContent(
            required: ["license_plate", "brand", "model", "type"],
            properties: [
                new OA\Property(property: "license_plate", type: "string"),
                new OA\Property(property: "brand", type: "string"),
                new OA\Property(property: "model", type: "string"),
                new OA\Property(property: "type", type: "string")
            ]
        )
    )]
    #[OA\Response(response: 201, description: "Created")]
    #[OA\Response(response: 422, description: "Validation Error")]
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'license_plate' => 'required|string|unique:vehicles',
            'brand' => 'required|string',
            'model' => 'required|string',
            'type' => 'required|string'
        ]);

        if ($validator->fails()) {
            return $this->errorResponse('Validation error', $validator->errors(), 422);
        }

        $vehicle = Vehicle::create($request->all());
        return $this->successResponse($vehicle, 'Data created successfully', 201);
    }
}
