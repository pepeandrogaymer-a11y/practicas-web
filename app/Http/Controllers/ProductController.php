<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ProductController extends Controller
{
    // Lista blanca de ordenamientos: clave de la URL => [columna, dirección]
    private const ORDENES = [
        'nombre_asc' => ['nombre', 'asc'],
        'nombre_desc' => ['nombre', 'desc'],
        'precio_desc' => ['precio', 'desc'],
        'precio_asc' => ['precio', 'asc'],
        'existencias_desc' => ['existencias', 'desc'],
        'existencias_asc' => ['existencias', 'asc'],
        'recientes' => ['created_at', 'desc'],
        'antiguos' => ['created_at', 'asc'],
    ];

    private const POR_PAGINA = [10, 15, 25, 50];

    public function index(Request $request)
    {
        $porPagina = (int) $request->query('por_pagina', 10);
        if (! in_array($porPagina, self::POR_PAGINA, true)) {
            $porPagina = 10;
        }

        $orden = $request->query('orden', 'nombre_asc');
        [$columna, $direccion] = self::ORDENES[$orden] ?? self::ORDENES['nombre_asc'];

        $products = Product::with('category') // una sola consulta extra para todas las categorías
            ->filter($request->only(['buscar', 'categoria', 'precio_min', 'precio_max', 'estado']))
            ->orderBy($columna, $direccion)
            ->orderBy('id') // desempate para que la paginación sea estable
            ->paginate($porPagina)
            ->withQueryString(); // conserva filtros, orden y cantidad al cambiar de página

        $categories = Category::orderBy('nombre')->get();

        return view('productos.index', compact('products', 'categories'));
    }

    public function create()
    {
        return view('productos.create', [
            'categories' => Category::orderBy('nombre')->get(),
        ]);
    }

    public function store(ProductRequest $request)
    {
        Product::create($request->validated());

        return redirect()->route('productos.index')
            ->with('status', 'Producto registrado correctamente.');
    }

    public function edit(Product $product)
    {
        return view('productos.edit', [
            'product' => $product,
            'categories' => Category::orderBy('nombre')->get(),
        ]);
    }

    public function update(ProductRequest $request, Product $product)
    {
        $product->update($request->validated());

        return redirect()->route('productos.index')
            ->with('status', 'Producto actualizado correctamente.');
    }

    public function destroy(Product $product)
    {
        $product->delete();

        return redirect()->route('productos.index')
            ->with('status', 'Producto eliminado correctamente.');
    }
}
