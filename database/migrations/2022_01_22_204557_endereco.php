<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class Endereco extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("endereco", function(Blueprint $table){
            $table->id();
            $table->string("cep", 15);
            $table->string("pais", 20);
            $table->string("uf", 50);
            $table->string("cidade", 50);
            $table->string("bairro", 50);
            $table->string("rua", 80);
            $table->integer("numero");

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
