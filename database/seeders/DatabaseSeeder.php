<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\Review;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        /* ================================================================
           USERS
           ================================================================ */
        $admin = User::factory()->create([
            'name'  => 'Admin Casainiers',
            'email' => 'admin@casainiers.mg',
        ]);

        $users = User::factory(5)->create();
        // On inclut l'admin dans la liste pour les reviews
        $allUsers = $users->push($admin);
        
        /* ================================================================
           CATÉGORIES
           ================================================================ */
        $categories = collect([
            ['name' => 'Processeurs',       'description' => 'CPU haute performance pour gaming et création.'],
            ['name' => 'Cartes Graphiques', 'description' => 'GPU pour le gaming, le rendu 3D et l\'IA.'],
            ['name' => 'Mémoire RAM',       'description' => 'Kits mémoire DDR4 et DDR5 haute fréquence.'],
            ['name' => 'Stockage SSD/HDD',  'description' => 'Solutions de stockage rapide NVMe et SATA.'],
            ['name' => 'Périphériques',      'description' => 'Souris, claviers, casques et écrans gaming.'],
            ['name' => 'Boîtiers & Alim',   'description' => 'Boîtiers ATX, alimentations modulaires certifiées.'],
        ])->map(function ($cat) {
            return Category::create([
                'name'        => $cat['name'],
                'slug'        => Str::slug($cat['name']),
                'description' => $cat['description'],
                'image'       => null,
            ]);
        });

        /* ================================================================
           PRODUITS
           ================================================================ */
        $productsData = [
            // ── Processeurs ──
            [
                'category' => 'Processeurs',
                'brand'    => 'AMD',
                'name'     => 'AMD Ryzen 9 7950X — 16 Cores / 32 Threads',
                'description' => 'Processeur haut de gamme AMD Zen 4 avec 16 cœurs et 32 threads. Fréquence boost jusqu\'à 5.7 GHz, 80 Mo de cache total, support DDR5 et PCIe 5.0.',
                'price'    => 2745000,
                'image'    => '/images/products/ryzen-9-7950x.png',
                'stock'    => 12,
            ],
            [
                'category' => 'Processeurs',
                'brand'    => 'Intel',
                'name'     => 'Intel Core i9-14900K — 24 Cores / 32 Threads',
                'description' => 'Processeur Intel Raptor Lake Refresh. 24 cœurs (8P+16E), boost jusqu\'à 6.0 GHz, support DDR4/DDR5.',
                'price'    => 2995000,
                'image'    => '/images/products/i9-14900k.png',
                'stock'    => 8,
            ],

            // ── Cartes Graphiques ──
            [
                'category' => 'Cartes Graphiques',
                'brand'    => 'NVIDIA',
                'name'     => 'NVIDIA GeForce RTX 5090 — 32 Go GDDR7',
                'description' => 'La carte graphique la plus puissante de NVIDIA. Architecture Blackwell, 32 Go de GDDR7, ray tracing de 5e génération et DLSS 4.0.',
                'price'    => 10995000,
                'image'    => '/images/products/rtx-5090.png',
                'stock'    => 4,
            ],
            [
                'category' => 'Cartes Graphiques',
                'brand'    => 'AMD',
                'name'     => 'AMD Radeon RX 9070 XT — 16 Go GDDR6',
                'description' => 'GPU AMD RDNA 4 avec 16 Go de GDDR6, ray tracing amélioré et FSR 4.0 pour des performances 1440p exceptionnelles.',
                'price'    => 3495000,
                'image'    => '/images/products/rx-9070xt.png',
                'stock'    => 15,
            ],

            // ── Mémoire RAM ──
            [
                'category' => 'Mémoire RAM',
                'brand'    => 'CORSAIR',
                'name'     => 'Corsair Dominator Titanium DDR5 64 Go — 6400 MT/s',
                'description' => 'Kit mémoire DDR5 haute performance. 64 Go (2x32 Go) à 6400 MT/s, profils XMP 3.0 optimisés.',
                'price'    => 1445000,
                'image'    => '/images/products/corsair-ddr5-64go.png',
                'stock'    => 22,
            ],
            [
                'category' => 'Mémoire RAM',
                'brand'    => 'G.SKILL',
                'name'     => 'G.Skill Trident Z5 RGB DDR5 32 Go — 7200 MT/s',
                'description' => 'Kit DDR5 haut de gamme avec éclairage RGB. 32 Go (2x16 Go) à 7200 MT/s, CL34, optimisé Intel XMP 3.0.',
                'price'    => 985000,
                'image'    => '/images/products/gskill-z5-rgb.png',
                'stock'    => 30,
            ],

            // ── Stockage SSD/HDD ──
            [
                'category' => 'Stockage SSD/HDD',
                'brand'    => 'SAMSUNG',
                'name'     => 'Samsung 990 PRO NVMe M.2 — 4 To PCIe Gen 4.0',
                'description' => 'SSD NVMe M.2 ultra rapide avec vitesse de lecture séquentielle jusqu\'à 7 450 Mo/s. Parfait pour les jeux et la vidéo 4K.',
                'price'    => 1645000,
                'image'    => '/images/products/samsung-990-pro-4to.png',
                'stock'    => 18,
            ],
            [
                'category' => 'Stockage SSD/HDD',
                'brand'    => 'WD',
                'name'     => 'WD Black SN850X — 2 To NVMe PCIe Gen 4.0',
                'description' => 'SSD gaming NVMe avec lecture jusqu\'à 7 300 Mo/s. Compatible PS5 et PC, dissipateur thermique inclus.',
                'price'    => 895000,
                'image'    => '/images/products/wd-sn850x.png',
                'stock'    => 25,
            ],

            // ── Périphériques ──
            [
                'category' => 'Périphériques',
                'brand'    => 'LOGITECH',
                'name'     => 'Logitech G Pro X Superlight 2 — Souris Gaming',
                'description' => 'Souris gaming sans fil ultra légère de 60g. Capteur HERO 2 à 44 000 DPI, autonomie de 95 heures.',
                'price'    => 645000,
                'image'    => '/images/products/logitech-gpx2.png',
                'stock'    => 40,
            ],
            [
                'category' => 'Périphériques',
                'brand'    => 'LG',
                'name'     => 'LG UltraGear 27GR95QE — 27" OLED 240Hz',
                'description' => 'Moniteur gaming OLED 27 pouces QHD avec taux de rafraîchissement de 240Hz et temps de réponse de 0.03ms.',
                'price'    => 4495000,
                'image'    => '/images/products/lg-27gr95qe.png',
                'stock'    => 6,
            ],

            // ── Boîtiers & Alim ──
            [
                'category' => 'Boîtiers & Alim',
                'brand'    => 'NZXT',
                'name'     => 'NZXT H9 Elite — Boîtier ATX Full Tower',
                'description' => 'Boîtier full tower avec triple panneau en verre trempé. Flux d\'air optimisé, support radiateur 360mm, câble management intégré.',
                'price'    => 985000,
                'image'    => '/images/products/nzxt-h9-elite.png',
                'stock'    => 14,
            ],
            [
                'category' => 'Boîtiers & Alim',
                'brand'    => 'CORSAIR',
                'name'     => 'Corsair RM1000x — Alimentation 1000W 80+ Gold',
                'description' => 'Alimentation modulaire 1000W certifiée 80 PLUS Gold. Ventilateur Zero RPM, câbles tressés, garantie 10 ans.',
                'price'    => 1195000,
                'image'    => '/images/products/corsair-rm1000x.png',
                'stock'    => 20,
            ],
        ];

        $products = collect();

        foreach ($productsData as $data) {
            $category = $categories->firstWhere('name', $data['category']);

            $product = Product::create([
                'category_id' => $category->id,
                'name'        => $data['name'],
                'slug'        => Str::slug($data['name']),
                'brand'       => $data['brand'],
                'description' => $data['description'],
                'price'       => $data['price'],
                'image'       => $data['image'],
                'stock'       => $data['stock'],
                'is_active'   => true,
            ]);

            $products->push($product);
        }

        /* ================================================================
           REVIEWS — Avis aléatoires (approuvés)
           ================================================================ */
        $comments = [
            5 => [
                'Excellent produit, je recommande vivement !',
                'Qualité irréprochable, livraison rapide.',
                'Parfait pour mon setup, aucun regret.',
                'Le meilleur rapport qualité/prix du marché.',
            ],
            4 => [
                'Très bon produit dans l\'ensemble.',
                'Bon rapport qualité/prix, petit bémol sur l\'emballage.',
                'Satisfait de mon achat, fonctionne comme attendu.',
            ],
            3 => [
                'Correct, sans plus. Fait le travail.',
                'Moyen, j\'attendais mieux pour ce prix.',
            ],
        ];

        foreach ($products as $product) {
            // Chaque produit reçoit entre 2 et 5 reviews
            $reviewers = $allUsers->random(rand(2, min(5, $allUsers->count())));

            foreach ($reviewers as $user) {
                $rating = collect([5, 5, 5, 4, 4, 3])->random();

                Review::create([
                    'product_id'  => $product->id,
                    'user_id'     => $user->id,
                    'rating'      => $rating,
                    'comment'     => $comments[$rating][array_rand($comments[$rating])],
                    'is_approved' => true,
                ]);
            }
        }
    }
}