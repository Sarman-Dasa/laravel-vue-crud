<?php

namespace App\Exports;

use App\Models\Todo;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\FromCollection;

class ExportTodo implements FromQuery, WithHeadings ,WithMapping
{
    use Exportable;
    /**
    * @return \Illuminate\Support\Collection
    */
    //return Todo::all();
    public function __construct($req)
    {
        // $this->start_date = $req->start_date;
        // $this->end_date = $req->end_date;
        $this->request = $req;
    }


    public function query()
    {
        $todo  = Todo::query();
        if($this->request->start_date && $this->request->end_date) {
            $todo->whereBetween('due_date',[$this->request->start_date,$this->request->end_date]);
        }
        $searchable_fields = ['title','description','priority']; 
        if($this->request->search) {
            $search = $this->request->search;
            $todo = $todo->where(function ($q) use ($search, $searchable_fields) {
                /* adding searchable fields to orwhere condition */
                foreach ($searchable_fields as $searchable_field) {
                    $q->orWhere($searchable_field, 'like', '%'.$search.'%');
                }
                $q->orWhereHas('user',function($query) {
                    $query->where('first_name','like',"%". $this->request->search."%");
                });
            });
        }
       
        return $todo;
    }
    // public function collection()
    // {
    //    return Todo::whereBetween('due_date',[$this->start_date,$this->end_date])->get();
    // }

    public function map($todo): array
    {
        if($todo->user) {
            $row['user_name'] = $todo->user->first_name;
        }
        $row['id'] = $todo->id;
        $row['title'] = $todo->title;
        $row['description'] = $todo->description;
        $row['due_date'] = $todo->due_date;
        $row['priority'] = $todo->priority;
        $row['status'] = $todo->status ? 'Done' : 'Undone';

        return array(
            $row['id'],
            $row['title'],
            $row['description'],
            $row['due_date'],
            $row['priority'],
            $row['status'],
            $row['user_name'],
        );
    }
    public function headings():array {
        return ['ID','title','description','due_date','priority','status','user_name'];
    }
}
