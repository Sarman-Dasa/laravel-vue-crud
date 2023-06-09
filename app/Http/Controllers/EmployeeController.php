<?php

namespace App\Http\Controllers;

use App\Models\Employee;
use Illuminate\Http\Request;
use App\Traits\ListingApiTrait;
use Dotenv\Validator;

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

        // add filter on employee joining date
        if($request->startDate && $request->endDate) {
            $query->whereBetween('joining_date',[$request->startDate,$request->endDate]);
        }

        //add filter on employee salary
        if($request->minSalary && $request->maxSalary) {
           $query->whereBetween('salary',[$request->minSalary, $request->maxSalary]);
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
    public function create(Request $request)
    {

        $request->validate([
            'name'      =>  'required|string',
            'email'     =>  'required|email|unique:employees,email',
            'phone'     =>  'required|unique:employees,phone|regex:"[6-9]{1}[0-9]{9}"',
            'salary'    =>  'required|numeric|min:10000|max:60000',
            'joining_date' => 'required|before_or_equal:'.now(),
        ]);

        Employee::create($request->only(['name', 'email', 'phone', 'salary', 'joining_date']));
        return ok("Employee data add successfully.");
    }

    /**
     * Display the specified resource.
     */
    public function get($id)
    {
        $employee = Employee::findOrFail($id);
        return ok('Employee data',$employee);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $request->validate([
            'name'      =>  'required|string',
            'email'     =>  'required|email|unique:employees,email,'.$id.',id',
            'phone'     =>  'required|unique:employees,phone,'.$id.',id|regex:"[6-9]{1}[0-9]{9}"',
            'salary'    =>  'required|numeric|min:10000|max:60000',
            'joining_date' => 'required|before_or_equal:'.now(),
        ]);

        Employee::findOrFail($id)->update($request->only(['name', 'email', 'phone', 'salary', 'joining_date']));
        return ok("Employee data updated successfully.");
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        Employee::findOrFail($id)->delete();

        return ok("Record deleted successfully.");
    }
}
