<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;
use App\Models\Category;

class CategoryController extends Controller
{
    public function __construct()
    {
        // Middleware untuk memastikan hanya admin yang bisa mengakses metode ini
        $this->middleware(function ($request, $next) {
            if (Auth::check() && Auth::user()->role === 'admin') {
                return $next($request);
            }
            return redirect('/home')->with('error', 'Anda tidak memiliki akses ke halaman ini');
        })->except(['showAll', 'apiStore', 'showByID', 'showByName', 'apiUpdate', 'apiDestroy']);
    }

    // Web methods
    public function index()
    {
        $categories = Category::all();
        return view('categories.index', compact('categories'));
    }

    public function create()
    {
        return view('categories.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        Category::create([
            'name' => $request->name,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category created successfully.');
    }

    public function edit($id)
    {
        $category = Category::find($id);

        if (!$category) {
            return redirect()->route('categories.index')->with('error', 'Category not found.');
        }

        return view('categories.edit', compact('category'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|max:255',
        ]);

        $category = Category::find($id);

        if (!$category) {
            return redirect()->route('categories.index')->with('error', 'Category not found.');
        }

        $category->update([
            'name' => $request->name,
        ]);

        return redirect()->route('categories.index')->with('success', 'Category updated successfully.');
    }

    public function destroy($id)
    {
        $category = Category::find($id);

        if ($category) {
            $category->delete();
            return redirect()->route('categories.index')->with('success', 'Category deleted successfully.');
        }

        return redirect()->route('categories.index')->with('error', 'Category not found.');
    }

    // API methods
    public function apiStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $payload = $validator->validated();

        Category::create([
            'name' => $payload['name']
        ]);

        return response()->json([
            'msg' => 'Data Category Berhasil Disimpan'
        ], 201);
    }

    public function showAll()
    {
        $categories = Category::all();

        return response()->json([
            'msg' => 'Data Category Keseluruhan',
            'data' => $categories
        ], 200);
    }

    public function showByID($id)
    {
        $category = Category::find($id);

        if ($category) {
            return response()->json([
                'msg' => 'Data Category dengan ID: ' . $id,
                'data' => $category
            ], 200);
        }

        return response()->json([
            'msg' => 'Data Category dengan ID: ' . $id . ' Tidak Ditemukan'
        ], 404);
    }

    public function showByName($name)
    {
        $categories = Category::where('name', 'LIKE', '%' . $name . '%')->get();

        if ($categories->count() > 0) {
            return response()->json([
                'msg' => 'Data Category dengan Nama yang mirip: ' . $name,
                'data' => $categories
            ], 200);
        }

        return response()->json([
            'msg' => 'Data Category dengan Nama yang mirip: ' . $name . ' Tidak Ditemukan'
        ], 404);
    }

    public function apiUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages(), 422);
        }

        $payload = $validator->validated();

        $category = Category::find($id);

        if ($category) {
            $category->update([
                'name' => $payload['name']
            ]);

            return response()->json([
                'msg' => 'Data Category Berhasil Diubah'
            ], 200);
        }

        return response()->json([
            'msg' => 'Data Category dengan ID: ' . $id . ' Tidak Ditemukan'
        ], 404);
    }

    public function apiDestroy($id)
    {
        $category = Category::find($id);

        if ($category) {
            $category->delete();

            return response()->json([
                'msg' => 'Data Category dengan ID: ' . $id . ' Berhasil Dihapus'
            ], 200);
        }

        return response()->json([
            'msg' => 'Data Category dengan ID: ' . $id . ' Tidak Ditemukan'
        ], 404);
    }
}
