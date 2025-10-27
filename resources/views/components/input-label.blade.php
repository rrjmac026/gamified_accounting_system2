@props(['value'])

<label {{ $attributes->merge(['class' => 'block text-sm font-semibold text-[#FF92C2] dark:text-[#FF92C2] mb-1']) }}>
    {{ $value ?? $slot }}
</label>
