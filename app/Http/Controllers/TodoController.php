<?php

namespace App\Http\Controllers;

use App\Http\Traits\ResponseTraits;
use App\Models\Todo;
use Illuminate\Http\Request;

class TodoController extends Controller
{
    use ResponseTraits;

    public function list()
    {
        $query = Todo::query();
        $searching_Fields = ['title','description'];
        return $this->sendFilterListData($query,$searching_Fields);
    }

    public function create(Request $request)
    {
        $validation = validator($request->all(),[
            'title'         =>  'required|min:5|max:50',
            'description'   =>  'required|min:1|max:100'
        ]);

        if($validation->fails())
            return $this->sendValidationError($validation);

        $todo = Todo::create($request->only(['title','description']));
        return $this->sendSuccessResponse('Todo Data Added Successfully');
    }

    public function update(Request $request ,$id)
    {
        $validation = validator($request->all(),[
            'title'         =>  'required|min:5|max:50',
            'description'   =>  'required|min:1|max:100',
        ]);

        if($validation->fails())
            return $this->sendValidationError($validation);

        $todo = Todo::findOrFail($id);
        $todo->update($request->only(['title','description']));
        return $this->sendSuccessResponse('Todo Data Updated Successfully');
    }

    public function get($id)
    {
        $todo = Todo::findOrFail($id);
        return $this->sendSuccessResponse('todo',$todo);
    }

    public function destroy($id)
    {
        $todo = Todo::findOrFail($id);
        $todo->delete();
        return $this->sendSuccessResponse('Todo data Deleted Successfully');
    }
}