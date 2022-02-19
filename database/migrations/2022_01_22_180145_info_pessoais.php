<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class InfoPessoais extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create("info_pessoais", function(Blueprint $table){
            $table->id();
            $table->string("nome_usuario", 150);
            $table->string("telefone", 20);
            $table->string("cpf", 15);
            $table->date("nascimento");

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
