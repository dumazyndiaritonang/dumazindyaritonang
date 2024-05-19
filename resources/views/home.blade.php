@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row justify-content-center">
        <div class="col-md-10">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>{{ __('Dashboard') }}</span>
                    <div class="d-flex">
                        <form action="{{ route('products.index') }}" method="GET" class="form-inline">
                            <input class="form-control mr-sm-2" type="search" name="search" placeholder="Search" aria-label="Search">
                            <button class="btn btn-outline-success my-2 my-sm-0" type="submit">Search</button>
                        </form>
                        @if (Auth::check() && Auth::user()->role === 'admin')
                            <a href="{{ route('categories.index') }}" class="btn btn-info ml-2">{{ __('Kelola Kategori') }}</a>
                            <a href="{{ route('users.index') }}" class="btn btn-warning ml-2">{{ __('Kelola User') }}</a>
                        @endif
                        <a href="{{ route('products.create') }}" class="btn btn-success ml-2">{{ __('Tambah Produk') }}</a>
                    </div>
                </div>

                <div class="card-body">
                    @if (session('status'))
                        <div class="alert alert-success" role="alert">
                            {{ session('status') }}
                        </div>
                    @endif

                    @if (session('success'))
                        @php
                            $successMessage = session('success');
                            $alertClass = 'alert-success'; // Default green

                            if (str_contains($successMessage, 'diubah')) {
                                $alertClass = 'alert-primary'; // Blue for update
                            } elseif (str_contains($successMessage, 'dihapus')) {
                                $alertClass = 'alert-danger'; // Red for delete
                            }
                        @endphp

                        <div class="alert {{ $alertClass }}" role="alert">
                            {{ session('success') }}
                        </div>
                    @endif

                    <table class="table mt-3">
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>{{ __('Name') }}</th>
                                <th>{{ __('Description') }}</th>
                                <th>{{ __('Price') }}</th>
                                <th>{{ __('Image') }}</th>
                                <th>{{ __('Category') }}</th>
                                <th>{{ __('Expired At') }}</th>
                                <th>{{ __('Modified By') }}</th>
                                <th>{{ __('Actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($products as $product)
                                <tr>
                                    <td>{{ $loop->iteration }}</td>
                                    <td>{{ $product->name }}</td>
                                    <td>{{ $product->description }}</td>
                                    <td>{{ number_format($product->price, 0, ',', '.') }}</td>
                                    <td>
                                        <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" width="50" class="img-thumbnail" data-bs-toggle="modal" data-bs-target="#imageModal" data-bs-src="{{ Storage::url($product->image) }}">
                                    </td>
                                    <td>{{ $product->category->name }}</td>
                                    <td>{{ $product->expired_at }}</td>
                                    <td>{{ $product->modified_by }}</td>
                                    <td>
                                        <a href="{{ route('products.edit', $product->id) }}" class="btn btn-primary btn-sm">{{ __('Edit') }}</a>
                                        <form action="{{ route('products.destroy', $product->id) }}" method="POST" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-danger btn-sm">{{ __('Delete') }}</button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="imageModalLabel">{{ __('Image') }}</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <img src="" alt="" class="img-fluid" id="modalImage">
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var imageModal = document.getElementById('imageModal');
        imageModal.addEventListener('show.bs.modal', function (event) {
            var button = event.relatedTarget;
            var src = button.getAttribute('data-bs-src');
            var modalImage = document.getElementById('modalImage');
            modalImage.src = src;
        });
    });
</script>
@endsection
