<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use App\Http\Controllers\Admin\AdminController;
use DB;
use App\Article;


class FrontPagesController extends AdminController
{
    public function index()
    {        
        $title = 'Страницы сайта';
        $articles = Article::all();
        return view('admin.front_pages.front_pages', ['title' => $title, 'articles' => $articles]);
    }


    public function frontPage($page_urn)
    {        
        $article = Article::where('urn', $page_urn)->get();
        return view('page', ['article' => $article[0]]);
    }


    public function addFrontPage()
    {       
        $title = 'Добавить страницу';
        
        return view('admin.front_pages.add_front_page', ['title' => $title]);
    }


    public function createFrontPage(Request $request)
    {        
		Article::create(['title' => $request->title, 'urn' => $request->urn, 'text' => $request->editor1]);

        return redirect()->route('frontPage', ['page_urn' => $request->urn]);
    }


    public function adminFrontPage($id)
    {       
        $title = 'Изменить страницу';
        $article = Article::find($id);
        
        return view('admin.front_pages.update_front_page', ['title' => $title, 'article' => $article]);
    }


    public function updateFrontPage(Request $request, $id)
    {        
		Article::where('id', $id)
      	->update(['title' => $request->title, 'urn' => $request->urn, 'text' => $request->editor1]);

        return redirect()->route('frontPage', ['page_urn' => $request->urn]);
    }


    public function deleteFrontPage(Request $request)
    {       
        $id = $request->input('action');

		DB::table('articles')
		->where('id', '=', $id)
		->delete();
        
        return redirect()->route('frontPages')->with('status', 'Страница успешно удалена!');
    }

}
