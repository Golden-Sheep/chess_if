<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class UsuariosTaxaDeVitoriaExport implements FromCollection,  WithHeadings
{

    public function __construct($collect)
    {
        $this->collect = $collect;
    }

    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return collect($this->collect);
    }

    public function headings(): array
    {
        return [
            'Jogador',
            'Quantidade de partidas',
            'Quantidade de vitória',
            'Taxa de vitória %',
        ];
    }
}
