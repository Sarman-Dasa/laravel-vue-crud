<?php

namespace App\Http\Controllers;

use App\Exports\ExportTodo;
use App\Http\Traits\ResponseTraits;
use App\Models\Todo;
use App\Traits\ListingApiTrait;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;


class TodoController extends Controller
{
    use ListingApiTrait;

    public function list(Request $request)
    {
        $this->ListingValidation();
        $query = Todo::query();
        $query->with('user');
        $searchable_fields = ['title','description']; 
        $data = $this->filterSearchPagination($query,$searchable_fields);

        return ok('Todo list',[
            'todos' =>  $data['query']->get(),
            'count' =>  $data['count'],
        ]);
    }

    public function create(Request $request)
    {
        $validation = validator($request->all(),[
            'title'         =>  'required|min:5|max:50',
            'description'   =>  'required|min:1|max:100',
            'status'        =>  'required|boolean',
            'priority'      =>  'required|in:high,low,medium',
            'due_date'      =>  'required|after_or_equal:'.now(),
            'user_id'       =>  'required|exists:users,id',
        ]);

        if($validation->fails())
            return error('validation error',$validation->errors(),'validation');

        $todo = Todo::create($request->only(['title','description','status','priority','due_date','user_id']));
        return ok('Todo Data Added Successfully');
    }

    public function update(Request $request ,$id)
    {
        $validation = validator($request->all(),[
            'title'         =>  'required|min:5|max:50',
            'description'   =>  'required|min:1|max:100',
            'status'        =>  'required|boolean',
            'priority'      =>  'required|in:high,low,medium',
            'due_date'      =>  'required|after_or_equal:'.now(),
            'user_id'       =>  'required|exists:users,id',
        ]);

        if($validation->fails())
            return error('validation error',$validation->errors(),'validation');

        $todo = Todo::findOrFail($id);
        $todo->update($request->only(['title','description','status','priority','due_date','user_id']));
        return ok('Todo Data Updated Successfully');
    }

    public function get($id)
    {
        $todo = Todo::with('user')->findOrFail($id);
        return ok('todo',$todo);
    }

    public function destroy($id)
    {
        $todo = Todo::findOrFail($id);
        $todo->delete();
        return ok('Todo data Deleted Successfully');
    }

    public function fileupload(Request $request) {

        $request->validate([
            'file' => 'required|image|mimes:png,jpg'
        ]);
        
        $fileName = $request->file->getClientOriginalName();
        $request->file->move(public_path('upload'), $fileName);
        return ok("File Uploaded Success Fully");
    }

    public function export(Request $request) {
        //return ok("Data export",$request->all());

       return Excel::download(new ExportTodo($request),'todo.csv');
    }

    public function status(Request $request, $id) {
        $request->validate([
            'status'        =>  'required|boolean',
        ]);

        Todo::findOrFail($id)->update($request->only('status'));

        return ok('status updated successfully');
    }
}