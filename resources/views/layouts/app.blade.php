<!doctype html>
<html lang="fa" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'مدیریت انبار' }}</title>
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
</head>
<body>

<div class="shell">

    <aside class="sidebar">

        <a class="brand" href="{{ route('dashboard') }}">
            <span class="brand-icon">▦</span>
            <span>مدیریت انبار</span>
        </a>

        <div class="userbox">
            <div class="avatar">
                {{ mb_substr(auth()->user()->name, 0, 1) }}
            </div>

            <div>
                <b>{{ auth()->user()->name }}</b>
                <small>
                    {{ auth()->user()->getRoleNames()->join('، ') }}
                </small>
            </div>
        </div>

        <nav>
            <a href="{{ route('dashboard') }}">▦ داشبورد</a>

            @can('warehouses.view')
                <a href="{{ route('warehouses.index') }}">▣ انبارها</a>
            @endcan

            @can('aisles.view')
                <a href="{{ route('aisles.index') }}">↔ راهروها</a>
            @endcan

            @can('racks.view')
                <a href="{{ route('racks.index') }}">▤ قفسه‌ها</a>
            @endcan

            @can('rows.view')
                <a href="{{ route('rows.index') }}">▦ ردیف‌ها</a>
            @endcan

            @can('products.view')
                <a href="{{ route('products.index') }}">◫ کالاها</a>
            @endcan

            @can('inventory.view')
                <a href="{{ route('inventory.index') }}">⇄ تخصیص و جابه‌جایی کالا</a>
            @endcan

            @can('tree.view')
                <a href="{{ route('tree.index') }}">⌘ درخت انبار</a>
            @endcan

            @can('layout.view')
                <a href="{{ route('layouts.index') }}">▧ نقشه شماتیک</a>
            @endcan

            @can('users.view')
                <a href="{{ route('users.index') }}">♙ کاربران</a>
            @endcan
        </nav>

        <form method="POST" action="{{ route('logout') }}" class="logout">
            @csrf
            <button type="submit">خروج از حساب</button>
        </form>

    </aside>

    <main class="main">

        <header class="topbar">
            <div>
                <strong>{{ $title ?? 'داشبورد' }}</strong>
                <small>سامانه مدیریت هوشمند انبار</small>
            </div>

            <span class="date">{{ now()->format('Y/m/d') }}</span>
        </header>

        <section class="content">

            @if(session('success'))
                <div class="alert success">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert danger">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @yield('content')

        </section>

        <footer>
            سامانه مدیریت انبار · نسخه اولیه
        </footer>

    </main>

</div>

@stack('scripts')

</body>
</html>
