<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Sistem Kitir</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 flex justify-center items-center h-screen">
    <div class="bg-white shadow-lg rounded-2xl p-8 w-96">
        <h2 class="text-2xl font-bold mb-6 text-center">Login Sistem Kitir</h2>
        @if ($errors->any())
            <div class="bg-red-100 text-red-700 p-2 mb-3 rounded">
                {{ $errors->first('login') }}
            </div>
        @endif
        <form method="POST" action="{{ route('login.process') }}">
            @csrf
            <div class="mb-4">
                <label class="block text-gray-700">Username</label>
                <input type="text" name="username" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200" required>
            </div>
            <div class="mb-4">
                <label class="block text-gray-700">Password</label>
                <input type="password" name="password" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:ring focus:ring-blue-200" required>
            </div>
            <button type="submit" class="w-full bg-blue-600 text-white py-2 rounded-lg hover:bg-blue-700">Login</button>
        </form>
    </div>
</body>
</html>
