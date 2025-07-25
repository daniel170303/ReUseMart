<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Login - ReuseMart</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>

<body class="bg-white font-sans">

    <div class="flex min-h-screen">

        <!-- Form Login -->
        <div class="w-full md:w-1/2 flex items-center justify-center px-6 lg:px-20">
            <div class="w-full max-w-md">

                <!-- Alert untuk Success Message -->
                @if (session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-check-circle mr-2"></i>
                            <span>{{ session('success') }}</span>
                        </div>
                    </div>
                @endif

                <!-- Alert untuk Error Message -->
                @if (session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                        <div class="flex items-center">
                            <i class="fas fa-exclamation-circle mr-2"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    </div>
                @endif

                <h2 class="text-4xl font-bold text-gray-900 mb-4">Welcome back!</h2>
                <p class="text-lg text-gray-600 mb-8">Enter your credentials to access your account</p>

                <form id="loginForm" method="POST" action="{{ route('login.submit') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-base font-medium text-gray-700">Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email') }}"
                            class="mt-2 block w-full px-4 py-3 text-base border @error('email') border-red-500 @else border-gray-300 @enderror rounded-md shadow-sm focus:ring-green-600 focus:border-green-600"
                            placeholder="Masukkan email Anda" />
                        @error('email')
                            <p class="text-red-600 text-sm mt-1 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-base font-medium text-gray-700">Password</label>
                        <div class="relative mt-2">
                            <input type="password" name="password" id="password"
                                class="block w-full px-4 py-3 text-base border @error('password') border-red-500 @else border-gray-300 @enderror rounded-md shadow-sm focus:ring-green-600 focus:border-green-600 pr-12"
                                placeholder="Masukkan password Anda" />
                            <button type="button" onclick="togglePassword()"
                                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-500 hover:text-gray-700 focus:outline-none">
                                <i id="eyeIcon" class="fa-solid fa-eye"></i>
                            </button>
                        </div>
                        @error('password')
                            <p class="text-red-600 text-sm mt-1 flex items-center">
                                <i class="fas fa-exclamation-triangle mr-1"></i>
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <!-- Tombol Login -->
                    <div>
                        <button type="submit"
                            class="w-full bg-green-700 text-white py-3 px-4 rounded-md hover:bg-green-800 transition font-semibold text-lg">
                            <i class="fas fa-sign-in-alt mr-2"></i>
                            Login
                        </button>
                    </div>
                </form>

                <!-- Daftar sekarang -->
                <div class="mt-6 text-center">
                    <p class="text-sm text-gray-600">
                        Belum punya akun?
                        <a href="{{ route('register') }}" class="text-green-700 font-semibold hover:underline">Daftar
                            sekarang!</a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Gambar -->
        <div class="hidden md:block md:w-1/2">
            <img src="{{ asset('images/GambarLoginRegister.png') }}" alt="Login Illustration"
                class="w-full h-screen object-cover">
        </div>
    </div>

    <!-- Script -->
    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const eyeIcon = document.getElementById('eyeIcon');

            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                eyeIcon.classList.remove('fa-eye');
                eyeIcon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                eyeIcon.classList.remove('fa-eye-slash');
                eyeIcon.classList.add('fa-eye');
            }
        }

        // Auto hide success/error alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.bg-green-100, .bg-red-100');
            alerts.forEach(function(alert) {
                alert.style.transition = 'opacity 0.5s';
                alert.style.opacity = '0';
                setTimeout(function() {
                    alert.remove();
                }, 500);
            });
        }, 5000);
    </script>

</body>

</html>
