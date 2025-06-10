@extends('layouts.app')

@section('title', $product->name . ' - Fruits & Légumes Bio')
@section('description', $product->short_description ?? Str::limit($product->description, 160))

@push('styles')
<style>
    .product-gallery {
        position: relative;
    }

    .main-image {
        width: 100%;
        height: 400px;
        object-fit: cover;
        border-radius: 15px;
        box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        background: linear-gradient(45deg, #f8f9fa, #e9ecef);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 8rem;
        color: var(--light-green);
    }

    .image-thumbnails {
        display: flex;
        gap: 10px;
        margin-top: 15px;
        overflow-x: auto;
        padding-bottom: 5px;
    }

    .thumbnail {
        width: 80px;
        height: 80px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        border: 3px solid transparent;
        transition: all 0.3s;
        flex-shrink: 0;
    }

    .thumbnail:hover, .thumbnail.active {
        border-color: var(--primary-green);
        transform: scale(1.05);
    }

    .product-badges {
        position: absolute;
        top: 20px;
        left: 20px;
        display: flex;
        flex-direction: column;
        gap: 10px;
    }

    .badge {
        padding: 8px 15px;
        border-radius: 20px;
        font-weight: 600;
        font-size: 0.85rem;
    }

    .badge-bio {
        background: var(--primary-green);
        color: white;
    }

    .badge-promo {
        background: var(--red);
        color: white;
    }

    .badge-new {
        background: var(--orange);
        color: white;
    }

    .product-info {
        padding-left: 30px;
    }

    .product-title {
        font-size: 2rem;
        font-weight: bold;
        color: #333;
        margin-bottom: 10px;
        line-height: 1.2;
    }

    .product-category {
        color: var(--primary-green);
        font-weight: 500;
        text-decoration: none;
        font-size: 0.9rem;
    }

    .product-category:hover {
        color: var(--dark-green);
    }

    .product-price-section {
        background: #f8f9fa;
        padding: 20px;
        border-radius: 15px;
        margin: 20px 0;
    }

    .price-current {
        font-size: 2.5rem;
        font-weight: bold;
        color: var(--primary-green);
        line-height: 1;
    }

    .price-old {
        font-size: 1.2rem;
        color: #999;
        text-decoration: line-through;
        margin-right: 10px;
    }

    .price-unit {
        font-size: 1rem;
        color: #666;
        margin-left: 5px;
    }

    .discount-badge {
        background: var(--red);
        color: white;
        padding: 5px 12px;
        border-radius: 15px;
        font-size: 0.85rem;
        font-weight: 600;
        margin-left: 15px;
    }

    .product-meta {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 15px;
        margin: 25px 0;
    }

    .meta-item {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 15px;
        background: #f8f9fa;
        border-radius: 10px;
    }

    .meta-icon {
        width: 35px;
        height: 35px;
        background: var(--primary-green);
        color: white;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
    }

    .meta-content h6 {
        margin: 0;
        font-size: 0.85rem;
        font-weight: 600;
        color: #333;
    }

    .meta-content p {
        margin: 0;
        font-size: 0.8rem;
        color: #666;
    }

    .stock-section {
        background: #fff3cd;
        border: 1px solid #ffeaa7;
        padding: 15px;
        border-radius: 10px;
        margin: 20px 0;
    }

    .stock-section.low-stock {
        background: #f8d7da;
        border-color: #f5c6cb;
    }

    .stock-section.out-of-stock {
        background: #f8d7da;
        border-color: #f5c6cb;
    }

    .add-to-cart-section {
        background: white;
        border: 2px solid var(--primary-green);
        border-radius: 15px;
        padding: 25px;
        margin: 25px 0;
    }

    .quantity-controls {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 20px;
    }

    .quantity-selector {
        display: flex;
        align-items: center;
        border: 2px solid #ddd;
        border-radius: 25px;
        overflow: hidden;
        background: white;
    }

    .quantity-btn {
        background: white;
        border: none;
        padding: 12px 18px;
        cursor: pointer;
        font-weight: bold;
        font-size: 1.1rem;
        transition: all 0.2s;
        color: var(--primary-green);
    }

    .quantity-btn:hover {
        background: var(--primary-green);
        color: white;
    }

    .quantity-input {
        border: none;
        text-align: center;
        width: 60px;
        padding: 12px 5px;
        font-size: 1.1rem;
        font-weight: bold;
        background: #f8f9fa;
    }

    .btn-add-to-cart {
        background: var(--primary-green);
        border: none;
        color: white;
        padding: 15px 40px;
        border-radius: 25px;
        font-size: 1.1rem;
        font-weight: 600;
        transition: all 0.3s;
        display: flex;
        align-items: center;
        gap: 10px;
        justify-content: center;
        flex: 1;
    }

    .btn-add-to-cart:hover {
        background: var(--dark-green);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(39
