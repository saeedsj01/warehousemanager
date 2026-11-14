<?php
namespace App\Http\Controllers;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\IOFactory;

class ProductController extends Controller
{
    public function index()
    {
        return view('products.index', ['items'=>Product::latest()->paginate(20)]);
    }

    public function store(Request $request)
    {
        $data = $request->validate(['sku'=>'required|max:80|unique:products,sku','name'=>'required|max:200','barcode'=>'nullable|max:100|unique:products,barcode','unit'=>'required|max:30','description'=>'nullable|max:3000']);
        Product::create($data + ['is_active'=>true]);
        return back()->with('success','کالا ثبت شد.');
    }

    public function update(Request $request, Product $product)
    {
        $data = $request->validate(['sku'=>'required|max:80|unique:products,sku,'.$product->id,'name'=>'required|max:200','barcode'=>'nullable|max:100|unique:products,barcode,'.$product->id,'unit'=>'required|max:30','description'=>'nullable|max:3000']);
        $product->update($data);
        return back()->with('success','کالا ویرایش شد.');
    }

    public function toggle(Product $product)
    {
        $product->is_active = ! $product->is_active;
        $product->save();
        return back()->with('success','وضعیت کالا تغییر کرد.');
    }

    public function importExcel(Request $request)
    {
        $request->validate(['file'=>'required|file|mimes:xlsx,xls,csv|max:10240']);
        abort_unless(class_exists(IOFactory::class), 500, 'برای ورود Excel ابتدا دستور composer install را اجرا کنید.');

        $sheet = IOFactory::load($request->file('file')->getRealPath())->getActiveSheet();
        $rows = $sheet->toArray(null, true, true, true);
        if (count($rows) < 2) {
            return back()->withErrors(['file'=>'فایل باید شامل سطر عنوان و حداقل یک کالا باشد.']);
        }

        $headers = array_map(fn($v)=>mb_strtolower(trim((string)$v)), array_shift($rows));
        $map = [];
        foreach ($headers as $column => $header) {
            $map[$header] = $column;
        }
        $aliases = [
            'sku'=>['sku','کد کالا','کدکالا'],
            'name'=>['name','نام کالا','نامکالا'],
            'barcode'=>['barcode','بارکد'],
            'unit'=>['unit','واحد'],
            'description'=>['description','توضیحات'],
        ];
        $columns = [];
        foreach ($aliases as $field=>$names) {
            foreach ($names as $alias) if (isset($map[$alias])) { $columns[$field] = $map[$alias]; break; }
        }
        if (!isset($columns['sku']) || !isset($columns['name'])) {
            return back()->withErrors(['file'=>'ستون‌های الزامی sku و name (یا کد کالا و نام کالا) در فایل پیدا نشد.']);
        }

        $prepared = [];
        foreach ($rows as $line=>$row) {
            $value = fn($field,$default=null)=>isset($columns[$field]) ? trim((string)($row[$columns[$field]] ?? '')) : $default;
            $sku = $value('sku');
            $name = $value('name');
            if ($sku === '' && $name === '') continue;
            if ($sku === '' || $name === '') {
                return back()->withErrors(['file'=>'کد یا نام کالا در ردیف '.($line+2).' خالی است.']);
            }
            $prepared[] = [
                'sku'=>$sku, 'name'=>$name,
                'barcode'=>$value('barcode') ?: null,
                'unit'=>$value('unit','عدد') ?: 'عدد',
                'description'=>$value('description') ?: null,
                'is_active'=>true, 'created_at'=>now(), 'updated_at'=>now(),
            ];
        }
        if (count($prepared) > 10000) {
            return back()->withErrors(['file'=>'حداکثر ۱۰٬۰۰۰ ردیف در هر بار قابل ورود است.']);
        }
        DB::transaction(function () use ($prepared) {
            foreach (array_chunk($prepared, 500) as $chunk) {
                foreach ($chunk as $product) {
                    Product::updateOrCreate(['sku'=>$product['sku']], $product);
                }
            }
        });
        return back()->with('success', count($prepared).' کالا از فایل Excel وارد یا بروزرسانی شد.');
    }

    public function destroy(Product $product)
    {
        abort_if($product->inventories()->exists(), 422, 'این کالا در موجودی انبار استفاده شده و قابل حذف نیست.');
        $product->delete();
        return back()->with('success','کالا حذف شد.');
    }
}
