<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use App\UserRole;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public function index()
    {
        abort_unless(auth()->user()->role === UserRole::ADMIN, 403);

        $categories = Category::withCount('books')->paginate(10);
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        abort_unless(auth()->user()->role === UserRole::ADMIN, 403);

        return view('categories.create');
    }

    public function store(CategoryRequest $request)
    {
        abort_unless(auth()->user()->role === UserRole::ADMIN, 403);

        Category::create($request->validated());

        return redirect()->route('categories.index')
            ->with('success', 'Category created successfully.');
    }

    public function show(Category $category)
    {
        $books = $category->books()->paginate(12);
        return view('categories.show', compact('category', 'books'));
    }

    public function edit(Category $category)
    {
        abort_unless(auth()->user()->role === UserRole::ADMIN, 403);

        return view('categories.edit', compact('category'));
    }

    public function update(CategoryRequest $request, Category $category)
    {
        abort_unless(auth()->user()->role === UserRole::ADMIN, 403);

        $category->update($request->validated());

        return redirect()->route('categories.index')
            ->with('success', 'Category updated successfully.');
    }

    public function destroy(Category $category)
    {
        abort_unless(auth()->user()->role === UserRole::ADMIN, 403);

        if ($category->books()->count() > 0) {
            return redirect()->route('categories.index')
                ->with('error', 'Cannot delete category with existing books.');
        }

        $category->delete();

        return redirect()->route('categories.index')
            ->with('success', 'Category deleted successfully.');
    }
}
