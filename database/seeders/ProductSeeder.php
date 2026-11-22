<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $categorias = collect(['Electrónica', 'Papelería', 'Hogar', 'Deportes', 'Juguetes'])
            ->map(fn ($nombre) => Category::firstOrCreate(['nombre' => $nombre]))
            ->values();

        for ($i = 1; $i <= 40; $i++) {
            $categoria = $categorias[($i - 1) % 5];
            $nombre = "Artículo {$categoria->nombre} #{$i}";

            $product = Product::firstOrNew(['nombre' => $nombre]);

            if (! $product->exists) {
                $existencias = $i % 4 === 0 ? 0 : (($i * 7) % 60) + 1;

                $product->fill([
                    'descripcion' => "Descripción de ejemplo del producto número {$i}.",
                    'category_id' => $categoria->id,
                    'precio' => 50 + (($i * 37) % 950),
                    'existencias' => $existencias,
                    'estado' => $existencias > 0 ? 'Disponible' : 'Agotado',
                ]);
                $product->created_at = now()->subDays($i);
                $product->save();
            }
        }
    }
}
