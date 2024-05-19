@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Daftar Produk</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Description</th>
                    <th>Price</th>
                    <th>Image</th>
                    <th>Category</th>
                    <th>Expired At</th>
                    <th>Modified By</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($products as $product)
                    <tr>
                        <td>{{ $product->id }}</td>
                        <td>{{ $product->name }}</td>
                        <td>{{ $product->description }}</td>
                        <td>{{ $product->price }}</td>
                        <td><img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" width="50"></td>
                        <td>{{ $product->category->name }}</td>
                        <td>{{ $product->expired_at }}</td>
                        <td>{{ $product->modified_by }}</td>
                        <td>
                            <a href="#" class="btn btn-primary btn-sm">Edit</a>
                            <a href="#" class="btn btn-danger btn-sm">Delete</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
