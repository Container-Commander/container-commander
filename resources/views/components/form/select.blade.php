@props([
    'keyValue' => 'false',
    'listings'
])

<select {{ $attributes->merge(['class' => 'shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline']) }}>
    <option value="">Select</option>
    @foreach ($listings as $key => $value)
        <option value="{{ $keyValue ? $key : $value }}">
            {{ $value }}
        </option>
    @endforeach
</select>