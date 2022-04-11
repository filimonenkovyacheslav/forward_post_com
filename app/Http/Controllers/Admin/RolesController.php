<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Hash;
use App\User;
use DB;


class RolesController extends AdminController
{
	public function index()
	{
		$title = 'Пользователи';
		$users = DB::select('select * from users');
		return view('admin.users', ['title' => $title,'users' => $users]);
	}


	public function showAdd()
	{
		$roles_arr = parent::ROLES_ARR;
		$title = 'Добавление пользователя';
		return view('admin.user_add', ['title' => $title, 'roles_arr' => $roles_arr]);
	}


	public function add(Request $request)
	{

		$new_user = new User();
		$fields = ['name', 'email', 'role'];

		foreach($fields as $field){
			$new_user->$field = $request->input($field);
		}

		$new_user->password = Hash::make($request->password);
		$new_user->save();

		return redirect()->route('adminUsers');
	}


	public function show($id)
	{
		$roles_arr = parent::ROLES_ARR;
		$user = User::find($id);
		$title = 'Изменение пользователя '.$user->name;

		return view('admin.user_update', ['title' => $title,'user' => $user, 'roles_arr' => $roles_arr]);
	}


	public function update(Request $request, $id)
	{

		$user_to_be_updated = User::find($id);
		$fields = ['name', 'email', 'role'];

		foreach($fields as $field){
			$user_to_be_updated->$field = $request->input($field);
		}

		if (!empty($request->password)) {
			$user_to_be_updated->password = Hash::make($request->password);
		}
		
		$user_to_be_updated->save();
		return redirect()->route('adminUsers');
	}


	public function destroy(Request $request)
	{
		$id = $request->input('action');
		$email = $request->input('email');
		DB::table('password_resets')
		->where('email', '=', $email)
		->delete();

		DB::table('users')
		->where('id', '=', $id)
		->delete();

		return redirect()->route('adminUsers');
	}
}
