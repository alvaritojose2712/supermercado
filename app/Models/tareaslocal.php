<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class tareaslocal extends Model
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
        "id_pedido",
        "tipo",
        "descripcion",
        "id_usuario",
        "estado",
        "valoraprobado",
    ];

    
    
    
    
}
