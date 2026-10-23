<?php

namespace Database\Seeders;

use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Recordings of an earlier run point at rows that migrate:fresh just dropped.
        Storage::disk(config('session-replay.storage.disk'))->deleteDirectory(config('session-replay.storage.directory'));
        file_put_contents(storage_path('logs/laravel.log'), '');

        User::factory()->create(['name' => 'Support', 'email' => 'support@acme.test', 'is_support' => true]);

        foreach ([
            ['Olivia Bennett', 'olivia@acme.test'],
            ['Marcus Reid', 'marcus@acme.test'],
            ['Priya Shah', 'priya@acme.test'],
            ['Tom Fischer', 'tom@acme.test'],
        ] as [$name, $email]) {
            User::factory()->create(['name' => $name, 'email' => $email]);
        }

        foreach ([
            ['A5 Dotted Notebook', 'Lay-flat binding, 160 pages of 100 gsm paper that takes fountain pen ink without bleeding.', 1800, 'amber'],
            ['Steel Fountain Pen', 'A brushed steel body with a fine nib and a converter, so any bottled ink works.', 4200, 'sky'],
            ['Desk Lamp', 'Warm, dimmable light on an arm that stays where you put it. USB-C powered.', 6900, 'rose'],
            ['Monitor Stand', 'Solid oak, raises the screen 12 cm and leaves room for a keyboard underneath.', 8900, 'emerald'],
            ['Wireless Mouse', 'Quiet clicks, three devices, a battery that lasts about two months.', 3900, 'violet'],
            ['Cable Organizer', 'A felt roll with elastic loops for chargers, cables and a spare adapter.', 1500, 'stone'],
        ] as [$name, $description, $price, $tone]) {
            Product::create(compact('name', 'description', 'price', 'tone'));
        }
    }
}
