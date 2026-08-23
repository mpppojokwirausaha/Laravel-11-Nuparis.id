<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToArray;

class GenericArrayImport implements ToArray
{
    public array $rows = [];

    public function array(array $rows): void
    {
        $this->rows = $rows;
    }
}
