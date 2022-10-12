<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCierresTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('cierres', function (Blueprint $table) {
            $table->increments('id');
            
            $table->decimal("debito",8,2); 
            $table->decimal("efectivo",8,2); 
            $table->decimal("transferencia",8,2); 

            $table->decimal("dejar_dolar",8,2); 
            $table->decimal("dejar_peso",8,2); 
            $table->decimal("dejar_bss",8,2);


            $table->decimal("efectivo_guardado",8,2);
            $table->decimal("efectivo_guardado_cop",8,2);
            $table->decimal("efectivo_guardado_bs",8,2);

            $table->decimal("tasa",8,2); 
            
            $table->text("nota")->nullable();
            
            $table->date("fecha")->unique();
            
            $table->integer("id_usuario");

            
            
            $table->integer("numventas")->default(0); 

            $table->decimal("precio",8,2)->default(0);
            $table->decimal("precio_base",8,2)->default(0);
            $table->decimal("ganancia",8,2)->default(0);
            $table->decimal("porcentaje",8,2)->default(0);
            $table->decimal("desc_total",8,2)->default(0);
            
            $table->boolean("push")->default(0);
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
        Schema::dropIfExists('cierres');
    }
}
