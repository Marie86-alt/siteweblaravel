@extends('layouts.app')

@section('title', $product->name . ' - Administration')

@section('content')
<div class="container py-4">
    <div class="row">
        <div class="col-md-10 mx-auto">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h4 class="mb-0">{{ $product->name }}</h4>
                    <div>
                        <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-warning">
                            <i class="fas fa-edit me-1"></i> Modifier
                        </a>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
                            <i class="fas fa-arrow-left me-1"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6>Informations générales</h6>
                            <table class="table table-borderless">
                                <tr>
                                    <td><strong>Nom :</strong></td>
                                    <td>{{ $product->name }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Catégorie :</strong></td>
                                    <td>{{ $product->category->name ?? 'Aucune' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Prix :</strong></td>
                                    <td>{{ number_format($product->price, 2, ',', ' ') }}€ / {{ $product->unit }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Stock :</strong></td>
                                    <td>{{ $product->stock_quantity }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Statut :</strong></td>
                                    <td>
                                        <span class="badge bg-{{ $product->is_active ? 'success' : 'danger' }}">
                                            {{ $product->is_active ? 'Actif' : 'Inactif' }}
                                        </span>
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <h6>Description</h6>
                            <p>{{ $product->description }}</p>

                            @if($product->origin)
                                <h6>Origine</h6>
                                <p>{{ $product->origin }}</p>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
