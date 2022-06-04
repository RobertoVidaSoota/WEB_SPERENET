<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Carrinho extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("carrinho", function(Blueprint $table){
            $table->id();
            $table->integer("quantidade_produto");
            $table->double("preco_acumulado", 50, 2);

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
