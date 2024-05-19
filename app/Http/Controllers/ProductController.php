<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('search');
        $products = Product::with('category')
            ->when($search, function ($query, $search) {
                return $query->where('name', 'like', "%{$search}%")
                    ->orWhereHas('category', function ($query) use ($search) {
                        $query->where('name', 'like', "%{$search}%");
                    });
            })
            ->paginate(15); // Menambahkan pagination dengan 15 item per halaman

        return view('home', compact('products'));
    }

    public function create()
    {
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'expired_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages())->setStatusCode(422);
        }

        $payload = $validator->validated();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        Product::create([
            'name' => $payload['name'],
            'description' => $payload['description'],
            'price' => $payload['price'],
            'image' => $imagePath,
            'category_id' => $payload['category_id'],
            'expired_at' => $payload['expired_at'],
            'modified_by' => Auth::user()->name
        ]);

        return redirect()->route('products.index')->with('success', 'Data Produk Berhasil Disimpan');
    }

    public function edit($id)
    {
        $product = Product::find($id);
        $categories = Category::all();

        if (!$product) {
            return redirect()->route('products.index')->with('error', 'Produk tidak ditemukan');
        }

        return view('products.edit', compact('product', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'expired_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages())->setStatusCode(422);
        }

        $payload = $validator->validated();

        $product = Product::find($id);

        if ($request->hasFile('image')) {
            // Hapus gambar lama dari penyimpanan
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            // Simpan gambar baru
            $imagePath = $request->file('image')->store('products', 'public');
            $product->update(['image' => $imagePath]);
        }

        $product->update([
            'name' => $payload['name'],
            'description' => $payload['description'],
            'price' => $payload['price'],
            'category_id' => $payload['category_id'],
            'expired_at' => $payload['expired_at'],
            'modified_by' => Auth::user()->name
        ]);

        return redirect()->route('products.index')->with('success', 'Data Produk Berhasil diubah');
    }

    public function destroy($id)
    {
        $product = Product::find($id);

        if ($product) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->delete();
            return redirect()->route('products.index')->with('success', 'Data Produk Berhasil dihapus');
        }

        return redirect()->route('products.index')->with('error', 'Produk tidak ditemukan');
    }

    // API Methods
    public function showAll()
    {
        $products = Product::with('category')->get();
        return response()->json(['msg' => 'Data Produk Keseluruhan', 'data' => $products], 200);
    }

    public function apiStore(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|integer',
            'image' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'expired_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages())->setStatusCode(422);
        }

        $payload = $validator->validated();

        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
        }

        $user = Auth::user();
        $modifiedBy = $user ? $user->name : $request->input('modified_by');

        $product = Product::create([
            'name' => $payload['name'],
            'description' => $payload['description'],
            'price' => $payload['price'],
            'image' => $imagePath,
            'category_id' => $payload['category_id'],
            'expired_at' => $payload['expired_at'],
            'modified_by' => $modifiedBy,
        ]);

        return response()->json(['msg' => 'Data Produk Berhasil Disimpan', 'data' => $product], 201);
    }

    public function showByID($id)
    {
        $product = Product::with('category')->find($id);

        if ($product) {
            return response()->json(['msg' => 'Data Produk dengan ID: ' . $id, 'data' => $product], 200);
        }

        return response()->json(['msg' => 'Data Produk dengan ID: ' . $id . ' Tidak Ditemukan'], 404);
    }

    public function showByName($name)
    {
        $product = Product::with('category')->where('name', 'LIKE', '%' . $name . '%')->get();

        if ($product->isNotEmpty()) {
            return response()->json(['msg' => 'Data Produk dengan Nama: ' . $name, 'data' => $product], 200);
        }

        return response()->json(['msg' => 'Data Produk dengan Nama: ' . $name . ' Tidak Ditemukan'], 404);
    }

    public function apiUpdate(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'required',
            'price' => 'required|integer',
            'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'category_id' => 'required|exists:categories,id',
            'expired_at' => 'required|date',
        ]);

        if ($validator->fails()) {
            return response()->json($validator->messages())->setStatusCode(422);
        }

        $payload = $validator->validated();

        $product = Product::find($id);

        if ($request->hasFile('image')) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $imagePath = $request->file('image')->store('products', 'public');
            $product->update(['image' => $imagePath]);
        }

        $product->update([
            'name' => $payload['name'],
            'description' => $payload['description'],
            'price' => $payload['price'],
            'category_id' => $payload['category_id'],
            'expired_at' => $payload['expired_at'],
            'modified_by' => Auth::user()->name
        ]);

        return response()->json(['msg' => 'Data Produk Berhasil diubah'], 200);
    }

    public function apiDestroy($id)
    {
        $product = Product::find($id);

        if ($product) {
            if ($product->image) {
                Storage::disk('public')->delete($product->image);
            }
            $product->delete();
            return response()->json(['msg' => 'Data Produk Berhasil dihapus'], 200);
        }

        return response()->json(['msg' => 'Data Produk dengan ID: ' . $id . ' tidak ditemukan'], 404);
    }

    public function __construct()
    {
        $this->middleware('auth')->except(['showAll', 'apiStore', 'showByID', 'showByName', 'apiUpdate', 'apiDestroy']);
    }

}
