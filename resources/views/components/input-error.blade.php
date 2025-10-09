@props(['messages'])

@if ($messages)
    <ul {{ $attributes->merge(['class' => 'text-sm text-red-600 space-y-1']) }}>
        @foreach ((array) $messages as $message)
            <li>
                @if (is_array($message))
                    {{ implode(', ', $message) }}
                @else
                    {{ $message }}
                @endif
            </li>
        @endforeach
    </ul>
@endif
