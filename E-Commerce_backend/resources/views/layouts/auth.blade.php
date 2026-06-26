<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Auth')</title>

    @include('partials.assets')
</head>

<body
    class="min-h-screen bg-[radial-gradient(circle_at_top,_rgba(34,197,94,0.16),_transparent_40%),linear-gradient(180deg,_#ffffff,_#f0fdf4)] text-slate-900">
    <main>
        <section>
            @yield('content')
        </section>
    </main>
</body>

</html>
