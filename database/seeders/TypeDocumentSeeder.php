<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\TypeDocument;


class TypeDocumentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        TypeDocument::create([
            'prefijo' => 'POS',
            'type' => 'INTERNA',
            'code' => '7',
            'fac_int' => '1',
        ]);
    }
}
