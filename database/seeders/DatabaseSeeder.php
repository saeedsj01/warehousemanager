<?php
namespace Database\Seeders;
use Illuminate\Database\Seeder;use App\Models\User;use Illuminate\Support\Facades\Hash;use Spatie\Permission\Models\Role;use Spatie\Permission\Models\Permission;
class DatabaseSeeder extends Seeder {public function run(): void {
 $groups=['dashboard'=>['view'],'warehouses'=>['view','create','edit','delete'],'aisles'=>['view','create','edit','delete'],'racks'=>['view','create','edit','delete'],'rows'=>['view','create','edit','delete'],'products'=>['view','create','edit','delete'],'inventory'=>['view','assign','move','remove'],'users'=>['view','create','edit','delete'],'roles'=>['view','create','edit','delete'],'tree'=>['view'],'layout'=>['view','design'],'qr'=>['view','print'],'reports'=>['view'],'movement'=>['view']];
 $all=[];foreach($groups as $group=>$actions)foreach($actions as $action)$all[]=$group.'.'.$action;foreach($all as $name)Permission::firstOrCreate(['name'=>$name,'guard_name'=>'web']);
 $admin=Role::firstOrCreate(['name'=>'مدیر کل','guard_name'=>'web']);$admin->syncPermissions($all);
 $manager=Role::firstOrCreate(['name'=>'مدیر انبار','guard_name'=>'web']);$manager->syncPermissions(array_values(array_filter($all,fn($p)=>!str_starts_with($p,'users.')&&!str_starts_with($p,'roles.'))));
 $operator=Role::firstOrCreate(['name'=>'اپراتور','guard_name'=>'web']);$operator->syncPermissions(['dashboard.view','warehouses.view','aisles.view','racks.view','rows.view','products.view','inventory.view','inventory.assign','inventory.move','tree.view','qr.view','movement.view']);
 $viewer=Role::firstOrCreate(['name'=>'مشاهده‌کننده','guard_name'=>'web']);$viewer->syncPermissions(array_values(array_filter($all,fn($p)=>str_ends_with($p,'.view'))));
 $user=User::firstOrCreate(['username'=>'admin'],['name'=>'مدیر سیستم','password'=>Hash::make('ChangeMe123!'),'is_active'=>true]);$user->syncRoles([$admin]);
 }}
