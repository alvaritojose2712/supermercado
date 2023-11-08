<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTareaslocalsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tareaslocals', function (Blueprint $table) {
            $table->increments("id");

            $table->integer("id_pedido")->unsigned()->nullable();
            $table->foreign('id_pedido')->references('id')->on('pedidos')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table->string("tipo");
            $table->string("descripcion");
            

            $table->integer("id_usuario")->unsigned();
            $table->foreign('id_usuario')->references('id')->on('usuarios')
            ->onDelete('cascade')
            ->onUpdate('cascade');

            $table->boolean("estado");
            $table->float("valoraprobado");

            
            $table->unique(["id_usuario", "id_pedido","tipo"]);


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
        Schema::dropIfExists('tareaslocals');
    }
}
