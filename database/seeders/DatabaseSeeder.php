<?php

namespace Database\Seeders;

use App\Models\Produto;
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
        $faker = Faker::create();

        // for($i = 1 ; $i <= 30 ; $i++){
        //     Produto::create([
        //         "nome_produto" => $faker->text(14),
        //         "link_imagem" => "sem link",
        //         "preco_produto" => "R$ ".rand(300, 9999).",".rand(13, 99)."",
        //         "descricao" => $faker->paragraph()
        //     ]);
        // }
    }
}
