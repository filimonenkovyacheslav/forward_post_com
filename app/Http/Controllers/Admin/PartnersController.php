<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use App\User;
use DB;


class PartnersController extends AdminController
{
	public function index()
	{
		$title = 'Партнеры';
		$viewer_arr = parent::VIEWER_ARR;
		$partners = DB::select('select * from partners');
		return view('admin.partners', ['title' => $title,'partners' => $partners, 'viewer_arr' => $viewer_arr]);
	}


	public function show($role)
	{
		$viewer_arr = parent::VIEWER_ARR;
		$partner = DB::table('partners')
			->where('role', '=', $role)
			->get();
		$name = '';

		if (count($partner)) {
			$name = $partner[0]->name;
		}
		
		$title = 'Изменение партнерa '.$name;

		return view('admin.partner_up', ['title' => $title,'name' => $name, 'role' => $role]);
	}


	public function update(Request $request, $role)
	{

		$partner = DB::table('partners')
			->where('role', '=', $role)
			->get();

		if (count($partner)) {
			DB::table('partners')
			->where('role', '=', $role)
			->update([
				'role' => $role,
				'name' => $request->name
			]);
		}
		else{
			DB::table('partners')->insert([
				'role' => $role,
				'name' => $request->name
			]);
		}
		
		return redirect()->route('adminPartners');
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
