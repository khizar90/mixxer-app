<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminCategoryController extends Controller
{
    public function list($type)
    {
        $categories = Category::where('type', $type)->get();

        if ($type == 'interest') {
            return view('panel-v1.category.interest', compact('categories'));
        }
        if ($type == 'degree') {
            return view('panel-v1.category.degree', compact('categories'));
        }
    }
    public function add(Request $request)
    {
        $category = new Category();
        $category->name = $request->name;
        $category->type = $request->type;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = Storage::disk('s3')->putFile('categories', $file);
            $path = Storage::disk('s3')->url($path);
            $category->image = $path;
        }
        $category->save();
        return redirect()->back();
    }

    public function edit(Request $request, $id)
    {
        $category = Category::find($id);
        // $image = public_path($category->image);
        // if(file_exists($image)){
        //     unlink($image);
        // }

        $category->name = $request->name;
        if ($request->hasFile('image')) {
            $file = $request->file('image');
            $path = Storage::disk('s3')->putFile('categories', $file);
            $path = Storage::disk('s3')->url($path);
            $category->image = $path;
        }
        $category->save();
        return redirect()->back();
    }

    public function delete($id)
    {
        $category = Category::find($id);
        $category->delete();
        return redirect()->back();
    }
}
