@extends('layouts.app')

@section('content')
    <div class="container">
        <h1>Daftar Kategori</h1>
        @if(session('success'))
            @php
                $successMessage = session('success');
                $alertClass = 'alert-success'; // Default green

                if (str_contains($successMessage, 'dihapus')) {
                    $alertClass = 'alert-danger'; // Red for delete
                }
            @endphp

            <div class="alert {{ $alertClass }}">
                {{ $successMessage }}
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif
        <a href="{{ route('categories.create') }}" class="btn btn-success mb-3">Tambah Kategori</a>
        <table class="table">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Name</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                    <tr>
                        <td>{{ $loop->iteration }}</td>
                        <td>{{ $category->name }}</td>
                        <td>
                            <a href="{{ route('categories.edit', $category->id) }}" class="btn btn-primary btn-sm">Edit</a>
                            <form action="{{ route('categories.destroy', $category->id) }}" method="POST" style="display:inline-block;">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
