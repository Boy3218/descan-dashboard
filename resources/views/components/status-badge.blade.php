@props(['status'])

@php
    $classes = [
        'approved' => 'bg-green-100 text-green-800',
        'in-review' => 'bg-yellow-100 text-yellow-800',
        'rejected' => 'bg-red-100 text-red-800',
        'pending' => 'bg-gray-100 text-gray-800',
    ][$status] ?? 'bg-gray-100 text-gray-800';

    $label = [
        'approved' => 'Approved',
        'in-review' => 'In Review',
        'rejected' => 'Rejected',
        'pending' => 'Pending',
    ][$status] ?? 'Pending';
@endphp

<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $classes }}">
    {{ $label }}
</span>
