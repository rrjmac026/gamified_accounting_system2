<section>
    <header>
        <h2 class="text-2xl font-semibold text-[#FF92C2]">
            Profile Information
        </h2>
        <p class="mt-1 text-sm text-gray-600">
            Update your account's profile information and email address.
        </p>
    </header>

    <form method="post" action="{{ route('profile.update') }}" class="mt-6 space-y-6">
        @csrf
        @method('patch')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-input-label for="first_name" value="First Name" class="text-[#FF92C2]" />
                <x-text-input id="first_name" name="first_name" type="text"
                    class="mt-1 block w-full rounded-lg border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200"
                    :value="old('first_name', $user->first_name)" required autofocus autocomplete="given-name" />
                <x-input-error class="mt-2" :messages="$errors->get('first_name')" />
            </div>

            <div>
                <x-input-label for="last_name" value="Last Name" class="text-[#FF92C2]" />
                <x-text-input id="last_name" name="last_name" type="text"
                    class="mt-1 block w-full rounded-lg border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200"
                    :value="old('last_name', $user->last_name)" required autocomplete="family-name" />
                <x-input-error class="mt-2" :messages="$errors->get('last_name')" />
            </div>
        </div>

        <div>
            <x-input-label for="email" value="Email" class="text-[#FF92C2]" />
            <x-text-input id="email" name="email" type="email"
                class="mt-1 block w-full rounded-lg border-[#FFC8FB] focus:border-pink-400 focus:ring focus:ring-pink-200"
                :value="old('email', $user->email)" required autocomplete="username" />
            <x-input-error class="mt-2" :messages="$errors->get('email')" />
        </div>

        <div class="flex items-center gap-4">
            <button type="submit" class="px-4 py-2 bg-[#FF92C2] text-white rounded-lg hover:bg-[#ff6fb5] transition-colors">
                Save
            </button>

            @if (session('status') === 'profile-updated')
                <p x-data="{ show: true }" x-show="show" x-transition x-init="setTimeout(() => show = false, 2000)"
                    class="text-sm text-green-600">Saved.</p>
            @endif
        </div>
    </form>
</section>
