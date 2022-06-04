<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Compras extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("compras", function(Blueprint $table){
            $table->id();
            $table->string("metodo_pagamento", 20)->nullable();
            $table->string("link_boleto", 300)->nullable();
            $table->dateTime("data_hora_compra")->nullable();
            $table->string("status", 50)->nullable();
            $table->string("local_entrega", 100)->nullable();
            $table->string("local_atual", 100)->nullable();

            $table->unsignedBigInteger("fk_id_usuario");
            $table->unsignedBigInteger("fk_id_carrinho");
            $table->foreign("fk_id_usuario")
                ->references("id")
                ->on("users")
                ->onUpdate("cascade")
                ->onDelete("cascade");
            $table->foreign("fk_id_carrinho")
                ->references("id")
                ->on("carrinho")
                ->onUpdate("cascade")
                ->onDelete("cascade");

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
        //
    }
}
