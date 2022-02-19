<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Comentarios extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("comentarios", function(Blueprint $table){
            $table->id();
            $table->text("texto_comentario");
            $table->double("estrelas", 2, 2);
            $table->enum("resposta", ["Y", "N"]);

            $table->unsignedBigInteger("fk_id_usuario");
            $table->unsignedBigInteger("fk_id_produto");
            $table->foreign("fk_id_usuario")
                ->references("id")
                ->on("users")
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
