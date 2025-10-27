<section>
    <header>
        <h2 class="text-2xl font-semibold text-[#FF92C2]">
            Update Password
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            Ensure your account is using a long, random password to stay secure.
        </p>
    </header>

    

    <form method="post" action="{{ route('profile.password.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div>
            <x-input-label for="current_password" value="Current Password" class="text-[#FF92C2]" />
            <x-text-input id="current_password" name="current_password" type="password" 
                class="mt-1 block w-full rounded-lg border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200" 
                autocomplete="current-password" />
            <x-input-error :messages="$errors->updatePassword->get('current_password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password" value="New Password" />
            <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password')" class="mt-2" />
        </div>

        <div>
            <x-input-label for="password_confirmation" value="Confirm Password" />
            <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" autocomplete="new-password" />
            <x-input-error :messages="$errors->updatePassword->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5] transition-colors">
                Update Password
            </button>

            @if (session('status') === 'password-updated')
                <script>
                    alert("Your password has been changed successfully!");
                </script>
            @endif
        </div>
    </form>
</section>

