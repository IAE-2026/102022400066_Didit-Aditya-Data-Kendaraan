<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class VehicleController extends Controller
{
    private function formatSuccess($message, $data, $code = 200)
    {
        return response()->json([
            'status' => 'success',
            'message' => $message,
            'data' => $data,
            'meta' => [
                'service_name' => 'Vehicle-Service',
                'api_version' => 'v1'
            ]
        ], $code);
    }

    private function formatError($message, $errors = null, $code = 400)
    {
        return response()->json([
            'status' => 'error',
            'message' => $message,
            'errors' => $errors
        ], $code);
    }

    #[OA\Get(
        path: '/api/v1/vehicles',
        summary: 'Get list of vehicles',
        tags: ['Vehicles'],
        security: [['ApiKeyAuth' => []]],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 401, description: 'Unauthorized')
        ]
    )]
    public function index()
    {
        $vehicles = \App\Models\Vehicle::all();
        return $this->formatSuccess('Daftar kendaraan berhasil diambil', $vehicles);
    }

    #[OA\Post(
        path: '/api/v1/vehicles',
        summary: 'Create a new vehicle',
        tags: ['Vehicles'],
        security: [['ApiKeyAuth' => []]],
        requestBody: new OA\RequestBody(
            required: true,
            content: new OA\JsonContent(
                required: ['license_plate'],
                properties: [
                    new OA\Property(property: 'license_plate', type: 'string', example: 'D 1234 ABC'),
                    new OA\Property(property: 'brand', type: 'string', example: 'Toyota'),
                    new OA\Property(property: 'type', type: 'string', example: 'SUV'),
                    new OA\Property(property: 'status', type: 'string', example: 'Tersedia'),
                ]
            )
        ),
        responses: [
            new OA\Response(response: 201, description: 'Successful operation'),
            new OA\Response(response: 422, description: 'Validation failed'),
            new OA\Response(response: 401, description: 'Unauthorized')
        ]
    )]
    public function store(Request $request)
    {
        $validator = \Illuminate\Support\Facades\Validator::make($request->all(), [
            'license_plate' => 'required|string|unique:vehicles,license_plate',
            'type' => 'nullable|string',
            'brand' => 'nullable|string',
            'status' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return $this->formatError('Validasi gagal', $validator->errors(), 422);
        }

        if (app()->environment('testing')) {
            $receiptNumber = 'IAE-LOG-TESTING-12345';
        } else {
            $soapService = new \App\Services\SoapAuditService();
            $receiptNumber = $soapService->sendAuditLog('VehicleCreated', $request->except(['_token']));
            
            if (!$receiptNumber) {
                return $this->formatError('Proses audit gagal: Layanan SOAP Audit tidak merespon.', null, 503);
            }
        }

        $vehicleData = $request->all();
        $vehicleData['receipt_number'] = $receiptNumber;
        $vehicle = \App\Models\Vehicle::create($vehicleData);

        if (!app()->environment('testing')) {
            $publisher = new \App\Services\RabbitMQPublisher();
            
            $vehicleArray = $vehicle->toArray();
            $vehicleArray['team_id'] = env('RABBITMQ_TEAM_NAME', 'TEAM-07');

            $publisher->publishEvent('vehicle.created', [
                'event' => 'vehicle.created',
                'timestamp' => now()->toIso8601String(),
                'data' => $vehicleArray
            ]);
        }

        return $this->formatSuccess('Data kendaraan berhasil ditambahkan', $vehicle, 201);
    }

    #[OA\Get(
        path: '/api/v1/vehicles/{id}',
        summary: 'Get specific vehicle',
        tags: ['Vehicles'],
        security: [['ApiKeyAuth' => []]],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path', required: true, schema: new OA\Schema(type: 'string'))
        ],
        responses: [
            new OA\Response(response: 200, description: 'Successful operation'),
            new OA\Response(response: 404, description: 'Not found'),
            new OA\Response(response: 401, description: 'Unauthorized')
        ]
    )]
    public function show(string $id)
    {
        $vehicle = \App\Models\Vehicle::find($id);
        if (!$vehicle) {
            return $this->formatError('Kendaraan tidak ditemukan', null, 404);
        }
        return $this->formatSuccess('Data spesifik kendaraan berhasil diambil', $vehicle);
    }
}
