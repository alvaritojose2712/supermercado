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
                "descripcion"=>"Titanio",
                "rif"=>"21628222-8",
                "direccion"=>"San Fernando",
                "telefono"=>"0",
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
