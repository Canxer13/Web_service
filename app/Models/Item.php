<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 * schema="Item",
 * type="object",
 * title="Item Model",
 * properties={
 * @OA\Property(property="id", type="integer", readOnly=true, example=1),
 * @OA\Property(property="name", type="string", example="Macbook Pro M3"),
 * @OA\Property(property="sku", type="string", example="MBP-M3-001"),
 * @OA\Property(property="quantity", type="integer", example=20),
 * @OA\Property(property="price", type="number", format="float", example=35000000.00),
 * @OA\Property(property="created_at", type="string", format="date-time", readOnly=true),
 * @OA\Property(property="updated_at", type="string", format="date-time", readOnly=true)
 * }
 * )
 */
class Item extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'sku',
        'quantity',
        'price',
    ];
}
