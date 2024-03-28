@props(['value', 'required' => 'false' ])

<label {{ $attributes->merge(['class' => 'block text-gray-700 text-sm font-bold mb-2']) }}>
    {{ $value ?? $slot }} <span class="text-red-600"> {{ ($required == 'true') ? '*' : '' }}</span>
</label>