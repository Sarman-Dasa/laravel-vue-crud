<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Traits\ListingApiTrait;
class EmployeeController extends Controller
{
    use ListingApiTrait;
    /**
     * Display a listing of the resource.
     */
    public function list(Request $request)
    {
        $this->ListingValidation();

        $query = Employee::query();

        $searchable_fields =['name','email','joining_date'];

        if($request->startDate && $request->endDate) {
            $query->whereBetween('joining_date',[$request->startDate,$request->endDate]);
        }

        $employees =  $this->filterSearchPagination($query, $searchable_fields);

        return ok('Employee list',[
            'employees' => $employees['query']->get(),
            'count'     =>  $employees['count']
        ]);


    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
