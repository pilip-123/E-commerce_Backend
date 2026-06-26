<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>E-Commerce</title>
    @include('partials.assets')
</head>

<body>
    <main>
        <section class="w-full rounded-3xl border border-emerald-100 bg-white/95 p-10 shadow-2xl shadow-emerald-950/10">
            @include('partials.brand', [
                'subtitle' => 'E-Commerce System',
                'title' => 'Green store. Simple flow.',
                'subtitleClass' => 'text-xs uppercase tracking-[0.35em] text-emerald-600',
                'titleClass' => 'mt-3 text-4xl font-black',
            ])
            <p class="mt-4 max-w-2xl text-slate-600">Sign in, shop, and place orders with less clutter.</p>
            <div class="mt-8 flex flex-wrap gap-3">
                @if (Route::has('login'))
                    <a class="rounded-full bg-emerald-600 px-5 py-3 font-semibold text-white"
                        href="{{ route('login') }}">Login</a>
                @endif
                @if (Route::has('register'))
                    <a class="rounded-full border border-emerald-200 px-5 py-3 font-semibold text-emerald-700"
                        href="{{ route('register') }}">Register</a>
                @endif
            </div>
        </section>
    </main>
</body>

</html>
