<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>@yield('title', 'Admin Panel')</title>
    @vite('resources/css/app.css') <!-- Penting -->
</head>

<body class="bg-gray-100 text-gray-900">
    <div class="flex">
        <main class="flex-1 p-6">
            @yield('content')
        </main>
    </div>
</body>

</html>
