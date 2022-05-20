<?php

namespace Database\Factories;

use App\Models\Produto;
use Illuminate\Database\Eloquent\Factories\Factory;

class FakeProdutosFactory extends Factory
{
    protected $model = Produto::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            "nome_produto" => $this->faker->text(14),
            "link_imagem" => "sem link",
            "preco_produto" => "R$ ".rand(300, 9999).",".rand(13, 99)."",
            "descricao" => $this->faker->paragraph()
        ];
    }
}
