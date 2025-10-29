<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistem Kitir</title>

    <script src="https://cdn.tailwindcss.com"></script>

    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-gray-50 min-h-screen flex flex-col">

    <nav class="bg-blue-700 shadow-md sticky top-0 z-40">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex-shrink-0">
                <a href="{{ route('kitir.index') }}" class="flex items-center space-x-2">
                    <img class="h-8 w-auto" src="{{ asset('images/logo.png') }}" alt="Logo Perusahaan">
                    <span class="font-bold text-xl text-white">Sistem Kitir</span>
                </a>
            </div>

            <div class="flex items-center space-x-4">
                @auth
                    <a href="{{ route('kitir.index') }}" class="text-blue-100 hover:text-white px-3 py-2 rounded-md text-sm font-medium transition">Daftar Kitir</a>
                    <span class="text-sm text-blue-200">
                        {{ Auth::user()->username }} ({{ ucfirst(Auth::user()->role) }})
                    </span>

                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button
                            type="submit"
                            class="bg-white text-blue-700 px-4 py-2 rounded-md text-sm font-semibold hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-offset-blue-700 focus:ring-white transition"
                        >
                            Logout
                        </button>
                    </form>
                @endauth
            </div>
        </div>
    </div>
</nav>

    <main class="flex-grow w-full max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        @yield('content')
    </main>

    <footer class="bg-white text-gray-600 text-center py-4 border-t border-gray-200">
        © {{ date('Y') }} Sistem Kitir. Semua hak dilindungi.
    </footer>

</body>
</html>