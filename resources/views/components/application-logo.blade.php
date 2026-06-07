@php
    $logoPath = file_exists(public_path('logo/logo.png'))
        ? 'logo/logo.png'
        : (file_exists(public_path('logo/full-logo.png')) ? 'logo/full-logo.png' : 'logo/logo.png');
@endphp

<img src="{{ asset($logoPath) }}" alt="{{ config('app.name', 'TimCare') }}" {{ $attributes->merge(['class' => 'block h-9 w-auto object-contain']) }} />
