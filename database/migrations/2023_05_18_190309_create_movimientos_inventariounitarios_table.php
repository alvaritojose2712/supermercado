<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMovimientosInventariounitariosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('movimientos_inventariounitarios', function (Blueprint $table) {
            $table->increments("id");

            $table->integer("id_producto")->unsigned();
            $table->foreign('id_producto')->references('id')
            ->on('inventarios')->onUpdate("cascade")->onDelete("cascade");
            $table->integer("id_pedido")->nullable();

            $table->integer("id_usuario")->unsigned();
            $table->foreign('id_usuario')->references('id')->on('usuarios');

            $table->integer("cantidad");
            $table->integer("cantidadafter");
            $table->string("origen");

            
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
        Schema::dropIfExists('movimientos_inventariounitarios');
    }
}
