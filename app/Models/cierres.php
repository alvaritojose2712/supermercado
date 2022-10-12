<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;


class cierres extends Model
{
    use HasFactory;
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }


    protected $fillable = [
        "debito",
        "efectivo",
        "transferencia",
        "dejar_dolar",
        "dejar_peso",
        "dejar_bss",
        "efectivo_guardado",
        "efectivo_guardado_cop",
        "efectivo_guardado_bs",
        "tasa",
        "nota",
        "id_usuario",
        "fecha",

        "numventas",
        "precio",
        "precio_base",
        "ganancia",
        "porcentaje",
        "desc_total",
    ];
}
