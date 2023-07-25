<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\TBlog_categoria;
use App\TBlog_post;
use Illuminate\Support\Facades\URL;
class BlogCategoryController extends Controller
{
    //
    public function index()
    {
        $categories=TBlog_categoria::paginate(10);
        return view('admin.blog_category', compact('categories'));
    }
    public function store(Request $request)
    {
        if ($request->filled(['txt_category'])){
            $cat = new TBlog_categoria();
            $cat->nombre = $request->input('txt_category');
            $cat->save();
            $host = $_SERVER["HTTP_HOST"];
            if(URL::previous()==('http://'.$host.'/admin/blog/create')){
                return redirect(route('admin_blog_create_path'))->with('status2', 'Category created successfully');
            }else{
                return redirect(route('admin_blog_category_index_path'))->with('status', 'Category created successfully');
            }
        }else{
            return "false";
        }  
    }
    public function update(Request $request, $id)
    {
        if ($request->filled(['txt_category'])){
            $cat = TBlog_categoria::FindOrFail($id);
            $cat->nombre = $request->input('txt_category');
            $cat->save();
            return redirect(route('admin_blog_category_index_path'))->with('status2', 'Successfully updated category');

        }else{
            return "false";
        }
    }
    public function destroy($id)
    {
        $cat=TBlog_categoria::find($id);
        $post=TBlog_post::where('categoria_id', $id)->count();
        if($post==0){
            $cat->delete();
            return redirect(route('admin_blog_category_index_path'))->with('delete', 'Category successfully removed');
        }else{
            return redirect(route('admin_blog_category_index_path'))->with('delete2', 'It cannot be deleted because there are posts that belong to that category');
        }
    }
}
