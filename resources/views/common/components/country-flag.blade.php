{{-- Country flag as inline SVG (4:3) by ISO 3166-1 alpha-2 code; add new flags to the match below --}}
@props([
    'code' => null,
    'title' => null,
])

@php
    $code = strtolower((string) $code);
@endphp

@if (in_array($code, ['hu', 'gb'], true))
    <svg
        xmlns="http://www.w3.org/2000/svg"
        viewBox="0 0 640 480"
        {{ $attributes->class(['h-3.5 w-auto shrink-0 rounded-[2px] ring-1 ring-black/10 dark:ring-white/15']) }}
        @if ($title) role="img" aria-label="{{ $title }}" @else aria-hidden="true" @endif
    >
        @if ($title)
            <title>{{ $title }}</title>
        @endif

        @switch($code)
            @case('hu')
                <path fill="#cd2a3e" d="M0 0h640v160H0z"/>
                <path fill="#fff" d="M0 160h640v160H0z"/>
                <path fill="#436f4d" d="M0 320h640v160H0z"/>
                @break

            @case('gb')
                <path fill="#012169" d="M0 0h640v480H0z"/>
                <path fill="#fff" d="m75 0 244 181L562 0h78v62L400 241l240 178v61h-80L320 301 81 480H0v-60l239-178L0 64V0z"/>
                <path fill="#c8102e" d="m424 281 216 159v40L369 281zm-184 20 6 35L54 480H0zM640 0v3L391 191l2-44L590 0zM0 0l239 176h-60L0 42z"/>
                <path fill="#fff" d="M241 0v480h160V0zM0 160v160h640V160z"/>
                <path fill="#c8102e" d="M0 193v96h640v-96zM273 0v480h96V0z"/>
                @break
        @endswitch
    </svg>
@endif
