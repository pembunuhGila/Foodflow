@php
    $successMessage = session('success') ?? session('status');
    $errorMessage = session('error') ?? session('danger');
    $warningMessage = session('warning');

    $isDeleted = false;
    if ($successMessage && is_string($successMessage)) {
        $lower = strtolower($successMessage);
        $isDeleted = str_contains($lower, 'hapus') || str_contains($lower, 'delete') || str_contains($lower, 'dihapus');
    }
@endphp

@if ($isDeleted)
    <div {{ $attributes->merge(['class' => 'no-print flex items-center gap-2 rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text shadow-sm']) }} role="alert">
        <x-icon name="trash" class="h-5 w-5 shrink-0 text-danger-text" />
        <span>{{ $successMessage }}</span>
    </div>
@elseif ($successMessage)
    <div {{ $attributes->merge(['class' => 'no-print flex items-center gap-2 rounded-input border border-success-text/20 bg-success-bg p-4 text-body text-success-text shadow-sm']) }} role="status">
        <x-icon name="check" class="h-5 w-5 shrink-0 text-success-text" />
        <span>{{ $successMessage }}</span>
    </div>
@endif

@if ($errorMessage)
    <div {{ $attributes->merge(['class' => 'no-print flex items-center gap-2 rounded-input border border-danger-text/20 bg-danger-bg p-4 text-body text-danger-text shadow-sm']) }} role="alert">
        <x-icon name="alert-circle" class="h-5 w-5 shrink-0 text-danger-text" />
        <span>{{ $errorMessage }}</span>
    </div>
@endif

@if ($warningMessage)
    <div {{ $attributes->merge(['class' => 'no-print flex items-center gap-2 rounded-input border border-warning-text/20 bg-warning-bg p-4 text-body text-warning-text shadow-sm']) }} role="alert">
        <x-icon name="alert" class="h-5 w-5 shrink-0 text-warning-text" />
        <span>{{ $warningMessage }}</span>
    </div>
@endif
