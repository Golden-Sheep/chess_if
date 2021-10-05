<?php

use Illuminate\Database\Seeder;

class UsuarioSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('usuarios')->insert([
            'nome'              => 'Administrador',
            'email'             => 'admin@hotmail.com',
            'password'          => 'admin123',
            'nivel_de_acesso'             => 1,
        ]);
    }
}
