<?php

namespace App\Http\Controllers;

/**
 * @OA\Info(
 * version="1.0.0",
 * title="Dokumentasi API Inventaris",
 * description="Dokumentasi lengkap untuk REST API manajemen inventaris barang. API ini mencakup autentikasi, manajemen item, dan sistem role & permission.",
 * @OA\Contact(
 * email="admin@inventaris.com"
 * )
 * )
 *
 * @OA\Server(
 * url=L5_SWAGGER_CONST_HOST,
 * description="Server API Utama"
 * )
 * @OA\SecurityScheme(
 * securityScheme="bearerAuth",
 * type="http",
 * scheme="bearer",
 * description="Masukkan Bearer Token yang didapat saat login.",
 * in="header",
 * name="Authorization"
 * )
 */
abstract class Controller
{
    //
}
