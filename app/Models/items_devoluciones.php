<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class items_devoluciones extends Model
{
    public function producto() { 
        return $this->hasOne('App\Models\inventario',"id","id_producto"); 
    }
}
