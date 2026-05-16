<?php

namespace App\Http\Controllers;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     title="Service Data Kendaraan (Vehicles)",
 *     version="1.0.0",
 *     description="API documentation for Data Kendaraan"
 * )
 * @OA\SecurityScheme(
 *     securityScheme="ApiKeyAuth",
 *     type="apiKey",
 *     in="header",
 *     name="X-IAE-KEY"
 * )
 */
abstract class Controller
{
}
