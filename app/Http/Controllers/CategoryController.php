<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('assets')->orderBy('name')->paginate(15);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => ['required','string','max:150','unique:categories,name'],
            'description' => ['nullable','string'],
            'status' => ['required',Rule::in(['active','inactive'])],
        ]);

        Category::create($data);

        return redirect()->route('categories.index')->with('success','Category created.');
    }

    public function edit(Category $category)
    {
        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, Category $category)
    {
        $data = $request->validate([
            'name' => ['required','string','max:150',Rule::unique('categories','name')->ignore($category->id)],
            'description' => ['nullable','string'],
            'status' => ['required',Rule::in(['active','inactive'])],
        ]);

        $category->update($data);

        return redirect()->route('categories.index')->with('success','Category updated.');
    }

    public function destroy(Category $category)
    {
        if ($category->assets()->exists()) {
            return back()->with('error','Cannot delete a category that has assets.');
        }

        $category->delete();

        return back()->with('success','Category deleted.');
    }
}
