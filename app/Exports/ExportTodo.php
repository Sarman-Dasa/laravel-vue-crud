<?php

namespace App\Exports;

use App\Models\Todo;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class ExportTodo implements FromCollection
{
    /**
    * @return \Illuminate\Support\Collection
    */
    //return Todo::all();
    public function __construct($req)
    {
        $this->start_date = $req->start_date;
        $this->end_date = $req->end_date;
    }

    public function collection()
    {
       return Todo::whereBetween('due_date',[$this->start_date,$this->end_date])->get();
    }

    // public function headings() {
    //     return ['ID','title','description','due_date','priority','status','user_id'];
    // }
}
