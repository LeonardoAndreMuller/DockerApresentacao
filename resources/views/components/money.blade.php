@props(['value'])

<span {{ $attributes->class('tabular-nums') }}>R$ {{ number_format((float) $value, 2, ',', '.') }}</span>
