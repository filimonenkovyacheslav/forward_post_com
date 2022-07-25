<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use Illuminate\Support\Facades\Auth;
use App\User;
use App\Worksheet;
use Transliterate;
use DB;

class IndexController extends AdminController
{
    public function index(){
        $title = 'Users';
        $users = DB::select('select * from users');
        $id = Auth::user()->id;
        $role = User::find($id)->role;

        if ($role !== 'user' && $role !== 'courier') {
            return view('admin.phil_ind.phil_ind_users', ['title' => $title,'users' => $users]);
        }
        elseif ($role === 'courier') {
            return redirect()->route('adminCourierTask');
        }
        else{
            return redirect()->route('welcome');
        }
    }


    public function philIndIndex(){
        $title = 'Users';
        $users = DB::select('select * from users');
        $id = Auth::user()->id;
        $role = User::find($id)->role;

        if ($role !== 'user') {
            return view('admin.phil_ind.phil_ind_users', ['title' => $title,'users' => $users]);
        }
        else{
            return redirect()->route('welcome');
        }
    }
}
