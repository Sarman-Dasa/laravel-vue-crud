<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\ListingApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    use ListingApiTrait;

    /**
     * Display a listing of the user.
     * @param \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function list()
    {
        $this->ListingValidation();
    
        $query = User::query();
        $query->with('role:id,role');
        $searchable_fields = ['first_name' , 'last_name','email' ,'phone']; 
        $data = $this->filterSearchPagination($query,$searchable_fields);

        return ok('User list',[
            'users' =>  $data['query']->get(),
            'count' =>  $data['count'],
        ]);
    }

    /**
     * Update the specified user in database.
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $user = auth()->user();
        $id = $user->id;
        $request->validate([
            'first_name'            =>  'required|string|min:3|max:30',
            'last_name'             =>  'required|string|min:3|max:30',
            'email'                 =>  'required|email|unique:users,email,' . $id . ',id',
            'phone'                 =>  'required|numeric|unique:users,phone,' . $id . ',id',
            'is_active'             =>  'required|boolean'
        ]);

        $user->update($request->only(['first_name', 'last_name', 'email', 'phone','is_active']));
        return ok('user data updated successfuly');
    }

    /**
     * Display the specified user.
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function get()
    {
        $user = User::findOrFail(auth()->user()->id);
        return ok('User profile', $user);
    }

    /**
     * logout the specified user.
     * @return \Illuminate\Http\Response
     */
    public function logout()
    {
        $user = auth()->user()->tokens();
        $user->delete();
    }

    /**
     * change the specified user password.
     *@param \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function changePassword(Request $request)
    {
        $request->validate([
            'current_password'      =>  'required|current_password',
            'password'              =>  'required|min:8|max:12',
            'password_confirmation' =>  'required|same:password',
        ]);

        $user = auth()->user();
        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return ok('Password changed successfully');
    }

     /**
     * Remove the specified user from database.
     * @return \Illuminate\Http\Response
     */
    public function destroy()
    {
        $user = auth()->user();
        $user->delete();

        return ok("Account deleted successfully");
    }

    /**
    * Update the specified user role from database
    */

    public function updateRole(Request $request, $id) {

        User::findOrFail($id)->update($request->only('role_id'));

        return ok('User role update successfully.');
    }
}
