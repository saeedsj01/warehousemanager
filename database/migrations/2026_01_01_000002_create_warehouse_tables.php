<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
 public function up(): void {
  Schema::create('warehouses',function(Blueprint $t){$t->id();$t->string('name');$t->string('code')->unique();$t->text('description')->nullable();$t->boolean('is_active')->default(true);$t->timestamps();});
  Schema::create('aisles',function(Blueprint $t){$t->id();$t->foreignId('warehouse_id')->constrained()->cascadeOnDelete();$t->string('name');$t->string('code');$t->text('description')->nullable();$t->boolean('is_active')->default(true);$t->timestamps();$t->unique(['warehouse_id','code']);});
  Schema::create('racks',function(Blueprint $t){$t->id();$t->foreignId('aisle_id')->constrained()->cascadeOnDelete();$t->string('name');$t->string('code');$t->text('description')->nullable();$t->boolean('is_active')->default(true);$t->timestamps();$t->unique(['aisle_id','code']);});
  Schema::create('storage_rows',function(Blueprint $t){$t->id();$t->foreignId('rack_id')->constrained()->cascadeOnDelete();$t->string('name');$t->string('code')->unique();$t->uuid('public_id')->unique();$t->text('description')->nullable();$t->boolean('is_active')->default(true);$t->timestamps();});
  Schema::create('products',function(Blueprint $t){$t->id();$t->string('sku')->unique();$t->string('name');$t->string('barcode')->nullable()->unique();$t->text('description')->nullable();$t->string('unit')->default('عدد');$t->boolean('is_active')->default(true);$t->timestamps();});
  Schema::create('row_inventories',function(Blueprint $t){$t->id();$t->foreignId('storage_row_id')->constrained('storage_rows')->cascadeOnDelete();$t->foreignId('product_id')->constrained()->restrictOnDelete();$t->decimal('quantity',14,3)->default(0);$t->timestamps();$t->unique(['storage_row_id','product_id']);});
  Schema::create('inventory_movements',function(Blueprint $t){$t->id();$t->foreignId('product_id')->constrained()->restrictOnDelete();$t->foreignId('from_row_id')->nullable()->constrained('storage_rows')->nullOnDelete();$t->foreignId('to_row_id')->nullable()->constrained('storage_rows')->nullOnDelete();$t->decimal('quantity',14,3);$t->foreignId('user_id')->nullable()->constrained()->nullOnDelete();$t->string('type',30);$t->text('note')->nullable();$t->timestamps();});
  Schema::create('warehouse_layouts',function(Blueprint $t){$t->id();$t->foreignId('warehouse_id')->unique()->constrained()->cascadeOnDelete();$t->unsignedInteger('canvas_width')->default(1200);$t->unsignedInteger('canvas_height')->default(700);$t->json('layout_data')->nullable();$t->timestamps();});
 }
 public function down(): void {foreach(['warehouse_layouts','inventory_movements','row_inventories','products','storage_rows','racks','aisles','warehouses'] as $t) Schema::dropIfExists($t);}
};
