<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - GAS</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-gradient-to-br from-[#FFE4F3] via-[#FFEEF2] to-[#FFF0F5] min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-md">
        <div class="bg-white/90 backdrop-blur-sm p-8 rounded-2xl shadow-xl border border-white/20">
            <h2 class="text-2xl font-bold text-[#FF92C2] mb-6 text-center">Reset Password</h2>
            
            @if (session('status'))
                <div class="mb-4 text-sm font-medium text-green-600 bg-green-50 border border-green-200 p-4 rounded-lg">
                    {{ session('status') }}
                </div>
            @endif

            <form method="POST" action="{{ route('password.email') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" id="email" required autofocus
                            class="w-full px-4 py-2 border-2 border-[#FFC8FB]/50 rounded-xl 
                                   focus:ring-[#FF92C2] focus:border-[#FF92C2] 
                                   bg-white/80 backdrop-blur-sm">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" 
                            class="w-full py-2 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] 
                                   hover:from-[#FFC8FB] hover:to-[#FF92C2] 
                                   text-white font-semibold rounded-xl 
                                   shadow-lg hover:shadow-xl transition-all duration-200">
                        Send Password Reset Link
                    </button>

                    <div class="text-center">
                        <a href="{{ route('login') }}" 
                           class="text-sm text-[#FF92C2] hover:text-[#FFC8FB] transition-colors">
                            Back to Login
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
