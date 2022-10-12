<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;


class clientes extends Model
{
    use HasFactory;
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

    public function pedidos() { 
        return $this->hasMany('App\Models\pedidos',"id_cliente","id"); 
    }
    protected $fillable = [
        "id",
        "identificacion",
        "nombre",
        "correo",
        "direccion",
        "telefono",
        "estado",
        "ciudad"
    ];
}
