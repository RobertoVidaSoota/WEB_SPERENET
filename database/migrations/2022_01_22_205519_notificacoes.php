<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Notificacoes extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("notificacoes", function(Blueprint $table){
            $table->id();
            $table->enum("promocoes", ["Y", "N"]);
            $table->enum("novidaades", ["Y", "N"]);
            $table->enum("atualizacoes", ["Y", "N"]);
            $table->enum("pedidos", ["Y", "N"]);

            $table->unsignedBigInteger("fk_id_usuario");
            $table->foreign("fk_id_usuario")
                ->references("id")
                ->on("users")
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
