<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateClientesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('clientes', function (Blueprint $table) {
            $table->increments('id');
            $table->string("identificacion",30)->unique();
            
            $table->string("nombre");
            $table->string("correo")->nullable();
            $table->text("direccion")->nullable();
            $table->string("telefono")->nullable();

            $table->string("estado")->nullable();
            $table->string("ciudad")->nullable();
            $table->timestamps();
        });
         DB::table("clientes")->insert([
            [
                "identificacion"=>"CF",
                "nombre"=>"CF",
                "correo"=>"CF",
                "direccion"=>"CF",
                "telefono"=>"CF",
                "estado"=>"CF",
                "ciudad"=>"CF",
            ],
            // [
            //     "identificacion"=>"26767116",
            //     "nombre"=>"Alvaro Ospino",
            //     "correo"=>"alvaroospino79",
            //     "direccion"=>"Mantecal",
            //     "telefono"=>"02409940793",
            //     "estado"=>"Apure",
            //     "ciudad"=>"Mantecal",
            // ],
            // [
            //     "identificacion"=>"12345678",
            //     "nombre"=>"Pedro Puerta",
            //     "correo"=>"pedropuerta79",
            //     "direccion"=>"Bruzual",
            //     "telefono"=>"02409940480",
            //     "estado"=>"Apure",
            //     "ciudad"=>"Bruzual",
            // ],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('clientes');
    }
}
