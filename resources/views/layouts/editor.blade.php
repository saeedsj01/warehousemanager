@extends('layouts.app')
@section('content')
<div class="panel">
    <div class="panel-head map-header">
        <div><h2>طراحی نقشه واقعی انبار: {{ $warehouse->name }}</h2><small class="muted">چیدمان راهروها، قفسه‌ها و موقعیت‌ها را روی پلان تنظیم کنید.</small></div>
        <button class="btn primary" id="save-layout" type="button">ذخیره نقشه</button>
    </div>
    <div class="map-toolbar">
        <button class="btn secondary" data-add="aisle" type="button">＋ افزودن راهرو</button>
        <button class="btn secondary" data-add="rack" type="button">＋ افزودن قفسه</button>
        <button class="btn secondary" data-add="row" type="button">＋ افزودن ردیف</button>
        <span class="toolbar-divider"></span>
        <button class="btn secondary" id="edit-item" type="button" disabled>ویرایش عنوان</button>
        <button class="btn secondary" id="rotate-item" type="button" disabled>چرخش ۹۰°</button>
        <button class="btn danger" id="delete-item" type="button" disabled>حذف انتخاب‌شده</button>
        <span class="map-hint">برای جابه‌جایی، شیء را بکشید؛ برای تغییر اندازه از گوشه آن استفاده کنید.</span>
    </div>
    <div class="map-legend">
        <span><i class="legend aisle"></i> راهرو</span>
        <span><i class="legend rack"></i> قفسه</span>
        <span><i class="legend row"></i> ردیف / محل نگهداری</span>
    </div>
    <div class="map-scroll">
        <div id="canvas" class="warehouse-plan" data-save="{{ route('layouts.save',$warehouse) }}" data-csrf="{{ csrf_token() }}">
            <div class="plan-wall wall-top"></div><div class="plan-wall wall-bottom"></div>
            <div class="plan-wall wall-left"></div><div class="plan-wall wall-right"></div>
            <div class="plan-label plan-title">پلان انبار · {{ $warehouse->name }}</div>
            <div class="plan-label plan-zone">محوطه عملیاتی</div>
            @foreach(($layout->layout_data ?? []) as $item)
                <div class="map-item {{ $item['kind'] ?? 'row' }}" data-kind="{{ $item['kind'] ?? 'row' }}" data-id="{{ $item['id'] ?? uniqid() }}" data-label="{{ $item['label'] ?? 'موقعیت' }}" data-rotation="{{ $item['rotation'] ?? 0 }}" style="left:{{ $item['x'] ?? 20 }}px;top:{{ $item['y'] ?? 80 }}px;width:{{ $item['width'] ?? (($item['kind']??'row')==='aisle'?420:($item['kind']==='rack'?180:110)) }}px;height:{{ $item['height'] ?? (($item['kind']??'row')==='aisle'?70:($item['kind']==='rack'?100:55)) }}px;transform:rotate({{ $item['rotation'] ?? 0 }}deg)">
                    <span class="map-item-label">{{ $item['label'] ?? 'موقعیت' }}</span><span class="resize-handle"></span>
                </div>
            @endforeach
        </div>
    </div>
    <p class="muted map-footnote">ابعاد پلان ۱۲۰۰ × ۷۰۰ واحد طراحی است. این نقشه یک چیدمان شماتیک قابل ویرایش است و جایگزین نقشه‌برداری یا اندازه‌گیری مهندسی ساختمان نیست.</p>
</div>
@endsection
@push('scripts')
<script>
(() => {
    const canvas = document.getElementById('canvas');
    if (!canvas) return;
    const $ = id => document.getElementById(id);
    let selected = null, drag = null;
    const limits = {width:1200,height:700};
    const defaults = {
        aisle:{label:'راهرو جدید',width:420,height:70},
        rack:{label:'قفسه جدید',width:180,height:100},
        row:{label:'ردیف جدید',width:110,height:55}
    };
    const updateTools = () => {
        ['edit-item','rotate-item','delete-item'].forEach(id => $(id).disabled = !selected);
        canvas.querySelectorAll('.map-item').forEach(el => el.classList.toggle('selected', el === selected));
    };
    const select = el => { selected = el; updateTools(); };
    const applyPosition = (el,x,y) => {
        const maxX = limits.width - el.offsetWidth, maxY = limits.height - el.offsetHeight;
        el.style.left = Math.max(0,Math.min(maxX,x))+'px';
        el.style.top = Math.max(0,Math.min(maxY,y))+'px';
    };
    const bind = el => {
        el.addEventListener('pointerdown', e => {
            if (e.target.classList.contains('resize-handle')) {
                select(el); drag = {type:'resize',el,startX:e.clientX,startY:e.clientY,w:el.offsetWidth,h:el.offsetHeight};
            } else {
                select(el); drag = {type:'move',el,startX:e.clientX,startY:e.clientY,x:el.offsetLeft,y:el.offsetTop};
            }
            el.setPointerCapture(e.pointerId); e.preventDefault();
        });
        el.addEventListener('pointermove', e => {
            if (!drag || drag.el !== el) return;
            const dx=e.clientX-drag.startX, dy=e.clientY-drag.startY;
            if (drag.type==='move') applyPosition(el,drag.x+dx,drag.y+dy);
            else {el.style.width=Math.max(35,Math.min(600,drag.w+dx))+'px';el.style.height=Math.max(25,Math.min(500,drag.h+dy))+'px';}
        });
        const stop=()=>drag=null;
        el.addEventListener('pointerup',stop);el.addEventListener('pointercancel',stop);
        el.addEventListener('click',()=>select(el));
    };
    canvas.querySelectorAll('.map-item').forEach(bind);
    document.querySelectorAll('[data-add]').forEach(btn => btn.addEventListener('click', () => {
        const kind=btn.dataset.add, d=defaults[kind], el=document.createElement('div');
        el.className='map-item '+kind;el.dataset.kind=kind;el.dataset.id='map-'+Date.now()+'-'+Math.floor(Math.random()*1000);
        el.dataset.label=d.label;el.dataset.rotation='0';el.style.width=d.width+'px';el.style.height=d.height+'px';el.style.left='40px';el.style.top='100px';
        el.innerHTML='<span class="map-item-label"></span><span class="resize-handle"></span>';el.querySelector('.map-item-label').textContent=d.label;
        canvas.appendChild(el);bind(el);select(el);
    }));
    $('edit-item').addEventListener('click',()=>{if(!selected)return;const label=prompt('عنوان جدید:',selected.dataset.label);if(label&&label.trim()){selected.dataset.label=label.trim();selected.querySelector('.map-item-label').textContent=label.trim();}});
    $('rotate-item').addEventListener('click',()=>{if(!selected)return;let n=(Number(selected.dataset.rotation||0)+90)%360;selected.dataset.rotation=n;selected.style.transform=`rotate(${n}deg)`;});
    $('delete-item').addEventListener('click',()=>{if(!selected||!confirm('این جزء از نقشه حذف شود؟'))return;selected.remove();select(null);});
    canvas.addEventListener('click',e=>{if(e.target===canvas)select(null);});
    $('save-layout').addEventListener('click',async()=>{
        const data=[...canvas.querySelectorAll('.map-item')].map(el=>({
            id:el.dataset.id,label:el.dataset.label,kind:el.dataset.kind,
            x:Math.round(el.offsetLeft),y:Math.round(el.offsetTop),
            width:Math.round(el.offsetWidth),height:Math.round(el.offsetHeight),
            rotation:Number(el.dataset.rotation||0)
        }));
        const button=$('save-layout');button.disabled=true;button.textContent='در حال ذخیره…';
        try{
            const response=await fetch(canvas.dataset.save,{method:'POST',headers:{'Content-Type':'application/json','X-CSRF-TOKEN':canvas.dataset.csrf,'Accept':'application/json'},body:JSON.stringify({layout_data:data})});
            const result=await response.json();if(!response.ok)throw new Error(result.message||'ذخیره انجام نشد');
            alert('نقشه با موفقیت ذخیره شد.');
        }catch(error){alert(error.message||'خطا در ذخیره نقشه. مجوز طراحی را بررسی کنید.');}
        finally{button.disabled=false;button.textContent='ذخیره نقشه';}
    });
    updateTools();
})();
</script>
@endpush
