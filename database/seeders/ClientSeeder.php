<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ClientSeeder extends Seeder
{
    public function run()
    {
        // Insertar un solo cliente con los datos proporcionados
        DB::table('clients')->insert([
            'nit' => '222222222222',         // NIT del cliente
            'dv' => 7,                        // Dígito de verificación
            'fullname' => 'Cliente Ocasional', // Nombre completo del cliente
            'phone' => '1111111',              // Teléfono del cliente
            'email' => 'noregistra@gmail.com', // Correo electrónico del cliente
        ]);
    }
}
