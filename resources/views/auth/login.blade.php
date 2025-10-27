<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - GAS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gradient-to-br from-[#FFE4F3] via-[#FFEEF2] to-[#FFF0F5] min-h-screen flex items-center justify-center p-4 sm:p-6 lg:p-8">
    <div class="bg-white/90 backdrop-blur-sm p-6 sm:p-8 rounded-2xl shadow-2xl w-full max-w-md border border-white/20">
        <div class="text-center mb-6 sm:mb-8">
            <!-- App Logo -->
            <div class="w-60 h-60 mx-auto mb-4">
                <img src="{{ asset('assets/app_logo.PNG') }}" alt="GAS Logo" 
                     class="w-full h-full object-contain transform hover:scale-105 transition-transform duration-300">
            </div>
            
            <h2 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] bg-clip-text text-transparent mb-2">
                Welcome Back!
            </h2>
            <p class="text-sm sm:text-base text-gray-600">Please sign in to your account</p>
        </div>

        <!-- Status Message -->
        @if (session('status'))
            <div class="mb-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                <p class="text-sm font-medium text-green-600">{{ session('status') }}</p>
            </div>
        @endif

        <form method="POST" action="{{ route('login') }}" class="space-y-4 sm:space-y-6">
            @csrf
            <div class="space-y-4">
                <!-- Email Field -->
                <div>
                    <label for="email" class="block text-sm font-semibold text-[#595758] mb-1 sm:mb-2">
                        <i class="fas fa-envelope text-[#FF92C2] mr-2"></i>Email Address
                    </label>
                    <div class="relative">
                        <input type="email" name="email" id="email" value="{{ old('email') }}" required autofocus
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border-2 border-[#FFC8FB]/50 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-[#FF92C2] focus:border-[#FF92C2] transition-all duration-200 bg-white/80 backdrop-blur-sm"
                            placeholder="Enter your email">
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <i class="fas fa-user text-[#FFC8FB]"></i>
                        </div>
                    </div>
                    @error('email')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-sm font-semibold text-[#595758] mb-1 sm:mb-2">
                        <i class="fas fa-lock text-[#FF92C2] mr-2"></i>Password
                    </label>
                    <div class="relative">
                        <input type="password" name="password" id="password" required
                            class="w-full px-3 sm:px-4 py-2.5 sm:py-3 text-sm sm:text-base border-2 border-[#FFC8FB]/50 rounded-xl shadow-sm focus:outline-none focus:ring-2 focus:ring-[#FF92C2] focus:border-[#FF92C2] transition-all duration-200 bg-white/80 backdrop-blur-sm"
                            placeholder="Enter your password">
                        <button type="button" class="absolute inset-y-0 right-0 pr-3 flex items-center" onclick="togglePassword()">
                            <i class="fas fa-eye text-[#FFC8FB] hover:text-[#FF92C2] transition-colors cursor-pointer" id="password-toggle"></i>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Remember Me & Forgot Password -->
                <div class="flex items-center justify-between text-sm sm:text-base">
                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="remember" 
                            class="h-4 w-4 text-[#FF92C2] focus:ring-[#FF92C2] border-[#FFC8FB] rounded transition-colors">
                        <label for="remember" class="ml-3 text-sm font-medium text-[#595758]">
                            Remember me
                        </label>
                    </div>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-medium text-[#FF92C2] hover:text-[#FFC8FB] transition-colors">
                            Forgot password?
                        </a>
                    @endif
                </div>

                <!-- Sign In Button -->
                <button type="submit" 
                    class="w-full py-2.5 sm:py-3 px-4 sm:px-6 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] hover:from-[#FFC8FB] hover:to-[#FF92C2] text-white text-sm sm:text-base font-semibold rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200">
                    <i class="fas fa-sign-in-alt mr-2"></i>
                    Sign In
                </button>
            </div>
        </form>

        <!-- Additional Links -->
        <div class="mt-4 sm:mt-6 text-center text-sm sm:text-base">
            <p class="text-sm text-gray-600">
                Don't have an account? 
                <a href="#" class="font-medium text-[#FF92C2] hover:text-[#FFC8FB] transition-colors">
                    Contact your administrator
                </a>
            </p>
        </div>

        <!-- Footer -->
        <div class="mt-6 sm:mt-8 text-center">
            <div class="flex items-center justify-center space-x-2 text-xs text-gray-500">
                <i class="fas fa-shield-alt text-[#FF92C2]"></i>
                <span>Secure login powered by GAS System</span>
            </div>
        </div>
    </div>

    <script>
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const toggleIcon = document.getElementById('password-toggle');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                toggleIcon.className = 'fas fa-eye-slash text-[#FFC8FB] hover:text-[#FF92C2] transition-colors cursor-pointer';
            } else {
                passwordInput.type = 'password';
                toggleIcon.className = 'fas fa-eye text-[#FFC8FB] hover:text-[#FF92C2] transition-colors cursor-pointer';
            }
        }
    </script>
</body>
</html>