<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientosInventariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimientos_inventarios', function (Blueprint $table) {
            $table->increments("id");
            
            $table->json("antes")->nullable();
            $table->json("despues")->nullable();
            
            $table->integer("id_producto")->nullable();
            $table->string("origen"); //Local //Central

            $table->integer("id_usuario")->unsigned();
            $table->foreign('id_usuario')->references('id')->on('usuarios');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('movimientos_inventarios');
    }
}
