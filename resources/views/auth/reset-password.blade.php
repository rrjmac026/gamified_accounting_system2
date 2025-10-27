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

            <form method="POST" action="{{ route('password.store') }}">
                @csrf
                <input type="hidden" name="token" value="{{ $request->route('token') }}">

                <div class="space-y-4">
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email Address</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $request->email) }}" required autofocus
                            class="w-full px-4 py-2 border-2 border-[#FFC8FB]/50 rounded-xl 
                                   focus:ring-[#FF92C2] focus:border-[#FF92C2] 
                                   bg-white/80 backdrop-blur-sm">
                        @error('email')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-1">New Password</label>
                        <input type="password" name="password" id="password" required
                            class="w-full px-4 py-2 border-2 border-[#FFC8FB]/50 rounded-xl 
                                   focus:ring-[#FF92C2] focus:border-[#FF92C2] 
                                   bg-white/80 backdrop-blur-sm">
                        @error('password')
                            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Confirm Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" required
                            class="w-full px-4 py-2 border-2 border-[#FFC8FB]/50 rounded-xl 
                                   focus:ring-[#FF92C2] focus:border-[#FF92C2] 
                                   bg-white/80 backdrop-blur-sm">
                    </div>

                    <button type="submit" 
                            class="w-full py-2 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] 
                                   hover:from-[#FFC8FB] hover:to-[#FF92C2] 
                                   text-white font-semibold rounded-xl 
                                   shadow-lg hover:shadow-xl transition-all duration-200">
                        Reset Password
                    </button>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
