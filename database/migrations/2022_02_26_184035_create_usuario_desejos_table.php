<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsuarioDesejosTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('usuario_desejos', function (Blueprint $table) {
            $table->id();
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
        Schema::dropIfExists('usuario_desejos');
    }
}
