<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProveedoresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('proveedores', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descripcion');
            $table->string('rif');
            $table->text('direccion');
            $table->string('telefono');

            $table->unique("rif");
            $table->unique("descripcion");

            $table->timestamps();
        });
        DB::table("proveedores")->insert([
            [
                "descripcion"=>"Proveedor 1",
                "rif"=>"010101012",
                "direccion"=>"Mantecal",
                "telefono"=>"0000030",
            ],
            [
                "descripcion"=>"Proveedor 2",
                "rif"=>"0101014101",
                "direccion"=>"Mantecal",
                "telefono"=>"0030000",
            ],
            [
                "descripcion"=>"Proveedor 3",
                "rif"=>"0134010101",
                "direccion"=>"Mantecal",
                "telefono"=>"0050000",
            ],
            [
                "descripcion"=>"Proveedor 4",
                "rif"=>"0154010101",
                "direccion"=>"Mantecal",
                "telefono"=>"0060000",
            ],
            [
                "descripcion"=>"Proveedor 5",
                "rif"=>"0105410101",
                "direccion"=>"Mantecal",
                "telefono"=>"0060000",
            ],
            [
                "descripcion"=>"Proveedor 6",
                "rif"=>"012010101",
                "direccion"=>"Mantecal",
                "telefono"=>"0070000",
            ],

            [
                "descripcion"=>"Proveedor 7",
                "rif"=>"441010101",
                "direccion"=>"Mantecal",
                "telefono"=>"00340000",
            ],
            [
                "descripcion"=>"Proveedor 8",
                "rif"=>"43301010101",
                "direccion"=>"Mantecal",
                "telefono"=>"04540000",
            ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('proveedores');
    }
}
