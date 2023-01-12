<?php

namespace Database\Seeders;

use App\Models\Comentarios;
use App\Models\Produto;
use App\Models\User;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class DatabaseSeeder extends Seeder
{
    // TIPOS DE DATA PARA FAKER
    //  ['Address', 'Barcode', 'Biased', 'Color', 'Company', 'DateTime', 'File', 'HtmlLorem', 'Image', 'Internet', 'Lorem', 'Medical', 'Miscellaneous', 'Payment', 'Person', 'PhoneNumber', 'Text', 'UserAgent', 'Uuid'];
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // User::factory(1)->create();

        // $faker = Faker::create();
        // $arrayUserId = array(1, 2, 6); juvenal83@example.org
        // $roundArray = array(0.0, 0.5, 1.0, 1.5, 2.0, 2.5, 3.0, 3.5, 4.0, 4.5, 5.0);
        // for($i = 1 ; $i <= 270 ; $i++){
        //     Comentarios::create([
        //         "texto_comentario" => $faker->text(40),
        //         "estrelas" => $roundArray[rand(0, 10)],
        //         "resposta" => "N",
        //         "fk_id_usuario" => $arrayUserId[rand(0, 2)],
        //         "fk_id_produto" => rand(1, 30),
        //     ]);
        // }
    }
}
