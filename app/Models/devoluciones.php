<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use DateTimeInterface;

class devoluciones extends Model
{
    protected function serializeDate(DateTimeInterface $date)
    {
        return $date->format('Y-m-d H:i:s');
    }

     public function vendedor() { 
        return $this->hasOne('App\Models\usuarios',"id","id_vendedor"); 
    }
    public function cliente() { 
        return $this->hasOne('App\Models\clientes',"id","id_cliente"); 
    }
    public function items() { 
        return $this->hasMany('App\Models\items_devoluciones',"id_devolucion","id"); 
    }
}
