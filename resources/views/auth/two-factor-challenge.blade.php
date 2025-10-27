<x-guest-layout>
    <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 
                bg-gradient-to-br from-[#FFE4F3] via-[#FFEEF2] to-[#FFF0F5] p-4 sm:p-6 lg:p-8">

        <div class="w-full sm:max-w-md px-6 py-6 sm:py-8 bg-white/90 backdrop-blur-sm rounded-2xl 
                    shadow-2xl border border-white/20">

            <!-- Header -->
            <div class="text-center mb-6 sm:mb-8">
                <div class="w-20 h-20 sm:w-24 sm:h-24 mx-auto mb-4 relative">
                    <div class="w-full h-full bg-gradient-to-br from-[#FF92C2] to-[#FFC8FB] 
                                rounded-2xl flex items-center justify-center shadow-lg">
                        <div class="text-white font-bold text-2xl tracking-wider">
                            <span class="block text-3xl">G</span>
                            <div class="flex text-lg -mt-1">
                                <span>A</span>
                                <span class="ml-1">S</span>
                            </div>
                        </div>
                    </div>
                    <div class="absolute -top-2 -right-2 w-6 h-6 bg-gradient-to-r from-[#FFC8FB] to-[#FF92C2] 
                                rounded-full flex items-center justify-center shadow-md">
                        <i class="fas fa-calculator text-white text-xs"></i>
                    </div>
                </div>

                <h2 class="text-2xl sm:text-3xl font-bold bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] 
                           bg-clip-text text-transparent mb-2">
                    Two-Factor Verification
                </h2>
                <p class="text-sm sm:text-base text-gray-600">
                    Enter the 6-digit code from your authenticator app to continue.
                </p>
            </div>

            <!-- Errors -->
            @if ($errors->any())
                <div class="mb-4 p-4 rounded-lg bg-red-50 border border-red-200">
                    <div class="font-medium text-red-800">{{ __('Oops! Verification failed.') }}</div>
                    <ul class="mt-2 list-disc list-inside text-sm text-red-600">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <!-- Form -->
            <form method="POST" action="{{ route('two-factor.login') }}" class="space-y-6">
                @csrf
                <div>
                    <label for="code" class="block text-sm font-medium text-gray-700">Authentication Code</label>
                    <div class="mt-1">
                        <input type="text" 
                            name="code" 
                            id="code" 
                            class="block w-full px-4 py-3 text-lg rounded-xl border-2 border-[#FFC8FB]/50 
                                   shadow-sm focus:ring-[#FF92C2] focus:border-[#FF92C2] transition-all duration-200 bg-white/80 backdrop-blur-sm" 
                            placeholder="Enter 6-digit code"
                            autofocus 
                            autocomplete="one-time-code"
                            inputmode="numeric"
                            pattern="[0-9]*"
                            maxlength="6">
                    </div>
                </div>

                <button type="submit" 
                    class="w-full py-3 px-4 bg-gradient-to-r from-[#FF92C2] to-[#FFC8FB] 
                           hover:from-[#FFC8FB] hover:to-[#FF92C2] text-white text-base font-semibold 
                           rounded-xl shadow-lg hover:shadow-xl transform hover:scale-[1.02] transition-all duration-200">
                    <i class="fas fa-check mr-2"></i>
                    Verify & Continue
                </button>
            </form>

            <!-- Back to login -->
            <div class="mt-6 text-center">
                <a href="{{ route('login') }}" 
                   class="text-sm font-medium text-[#FF92C2] hover:text-[#FFC8FB] transition-colors">
                    ← Back to login
                </a>
            </div>
        </div>
    </div>
</x-guest-layout>
