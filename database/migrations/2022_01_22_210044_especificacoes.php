<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Especificacoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("especificacoes", function(Blueprint $table){
            $table->id();
            $table->string("nome_escecificacao", 30);
            $table->string("valor_especificacao", 30);

            $table->unsignedBigInteger("fk_id_produto");
            $table->foreign("fk_id_produto")
                ->references("id")
                ->on("produto")
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
