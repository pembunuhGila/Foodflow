@props(['value' => 0, 'max' => 100, 'tone' => 'primary'])
@php
$percent = $max > 0 ? max(0, min(100, round($value / $max * 100))) : 0;
$bar = ['primary' => 'bg-primary', 'warning' => 'bg-warning-text', 'danger' => 'bg-danger-text', 'success' => 'bg-success-text'][$tone] ?? 'bg-primary';
@endphp
<div class="h-1.5 w-full overflow-hidden rounded-full bg-primary/10" role="progressbar"
    aria-valuenow="{{ $percent }}" aria-valuemin="0" aria-valuemax="100">
    <div class="h-full rounded-full {{ $bar }}" @style(["width: {$percent}%"])></div>
</div>