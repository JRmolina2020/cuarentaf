<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
  /**
   * Seed the application's database.
   *
   * @return void
   */
  public function run()
  {
    // Llamar a todos los seeders
    $this->call([
      ClientSeeder::class,
      TypeDocumentSeeder::class,
      MoneySeeder::class,
      PermissionSeeder::class,
      // Agrega más seeders si los tienes
    ]);
  }
}
