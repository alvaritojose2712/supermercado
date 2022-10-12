<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCategoriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('categorias', function (Blueprint $table) {
            $table->increments('id');
            $table->string('descripcion');

            $table->timestamps();
        });
         DB::table("categorias")->insert([
            ["descripcion"=>"ACEITE/MARGARINA/MANTEQUILLA"],
            ["descripcion"=>"ACEITES"],
            ["descripcion"=>"AGRICOLA"],
            ["descripcion"=>"ALAMBRE"],
            ["descripcion"=>"ALIMENTOS"],
            ["descripcion"=>"BATERIA"],
            ["descripcion"=>"BEBIDAS ALCOHÓLICAS"],
            ["descripcion"=>"BEBIDAS NO ALCOHÓLICAS"],
            ["descripcion"=>"BISUTERIA"],
            ["descripcion"=>"CALZADO"],
            ["descripcion"=>"CARNES"],
            ["descripcion"=>"CERAMICA"],
            ["descripcion"=>"CERRADURA"],
            ["descripcion"=>"CIGARRILLOS"],
            ["descripcion"=>"COMESTIBLES"],
            ["descripcion"=>"COMIDAS CONGELADAS"],
            ["descripcion"=>"COMIDAS RAPIDAS"],
            ["descripcion"=>"CONDIMENTOS Y SAZONADORES"],
            ["descripcion"=>"CONSTRUCCION"],
            ["descripcion"=>"CORDONES"],
            ["descripcion"=>"COSMETICOS"],
            ["descripcion"=>"CUIDADO DEL HOGAR"],
            ["descripcion"=>"CUIDADO PERSONAL"],
            ["descripcion"=>"DISCO"],
            ["descripcion"=>"DULCES"],
            ["descripcion"=>"ELECTRICIDAD"],
            ["descripcion"=>"ELECTRODOMESTICO"],
            ["descripcion"=>"ELECTRONICA"],
            ["descripcion"=>"FITNESS"],
            ["descripcion"=>"FONTANERIA"],
            ["descripcion"=>"GAS"],
            ["descripcion"=>"GRIFERIA"],
            ["descripcion"=>"HARINAS"],
            ["descripcion"=>"HERRAMIENTAS"],
            ["descripcion"=>"HERRERIA"],
            ["descripcion"=>"HOGAR"],
            ["descripcion"=>"HUEVO"],
            ["descripcion"=>"ILUMINACION"],
            ["descripcion"=>"INTERNET"],
            ["descripcion"=>"JARDINERIA"],
            ["descripcion"=>"LENTES"],
            ["descripcion"=>"MACRO SNARCKS"],
            ["descripcion"=>"MALLAS"],
            ["descripcion"=>"MECANICA"],
            ["descripcion"=>"MEDICAMENTOS"],
            ["descripcion"=>"MOTOS"],
            ["descripcion"=>"NAILOS"],
            ["descripcion"=>"CONFITERÍA"],
            ["descripcion"=>"PAÑOS ABSORVENTES"],
            ["descripcion"=>"PEGAS"],
            ["descripcion"=>"PERFUMES"],
            ["descripcion"=>"PESCA"],
            ["descripcion"=>"PINTURA"],
            ["descripcion"=>"PLANTAS"],
            ["descripcion"=>"PLOMERIA"],
            ["descripcion"=>"PRODUCTOS LÁCTEOS"],
            ["descripcion"=>"QUINCALLERIA"],
            ["descripcion"=>"REFRIGERACION"],
            ["descripcion"=>"REPUESTOS"],
            ["descripcion"=>"ROPA"],
            ["descripcion"=>"SALSAS"],
            ["descripcion"=>"SOPAS"],
            ["descripcion"=>"SORTEOS, LOTERÍAS Y APUESTAS"],
            ["descripcion"=>"SUPLEMENTOS ALIMENTICIOS"],
            ["descripcion"=>"TECNOLOGIA"],
            ["descripcion"=>"TELEFONIA"],
            ["descripcion"=>"TERMOS"],
            ["descripcion"=>"TORNILLERIA"],
            ["descripcion"=>"VETERINARIA"],
            ["descripcion"=>"VITAMINAS"],

        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('categorias');
    }
}
