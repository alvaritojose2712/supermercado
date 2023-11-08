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

    public function usuario() { 
        return $this->hasOne(\App\Models\usuarios::class,"id","id_usuario"); 
    }


    protected $fillable = [
        "debito",
        "efectivo",
        "transferencia",
        "caja_biopago",
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
        "efectivo_actual",
        "efectivo_actual_cop",
        "efectivo_actual_bs",
        "puntodeventa_actual_bs",

        "tasacop",
        "inventariobase",
        "inventarioventa",
        "numreportez",
        "ventaexcento",
        "ventagravadas",
        "ivaventa",
        "totalventa",
        "ultimafactura",
        "credito",
        "creditoporcobrartotal",
        "vueltostotales",
        "abonosdeldia",
        "efecadiccajafbs",
        "efecadiccajafcop",
        "efecadiccajafdolar",
        "efecadiccajafeuro",
    ];
}
