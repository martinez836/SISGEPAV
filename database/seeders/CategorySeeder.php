<?php

// database/seeders/CategorySeeder.php
namespace Database\Seeders;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder {
    public function run(): void {
        foreach (['A','AA','AAA','SUPER','YEMAS'] as $name) {
        DB::table('categories')->updateOrInsert(
            ['categoryName'=>$name],
            ['created_at'=>now(),'updated_at'=>now()]
        );
        }
    }
}

