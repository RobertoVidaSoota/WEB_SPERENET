<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ProdutoCategoria extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("produto_categoria", function(Blueprint $table){
            $table->id();

            $table->unsignedBigInteger("fk_id_categoria");
            $table->unsignedBigInteger("fk_id_produto");
            $table->foreign("fk_id_categoria")
                ->references("id")
                ->on("categoria")
                ->onUpdate("cascade")
                ->onDelete("cascade");
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
