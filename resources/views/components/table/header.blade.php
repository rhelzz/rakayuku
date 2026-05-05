@props([
    'label',
    'field',
    'sortField' => request('sort_field'),
    'sortDir' => request('sort_dir', 'desc'),
    'align' => 'left'
])

@php
    $isSorted = $sortField === $field;
    $nextDir = $isSorted && $sortDir === 'asc' ? 'desc' : 'asc';
    $icon = 'unfold_more';
    
    if ($isSorted) {
        $icon = $sortDir === 'asc' ? 'expand_less' : 'expand_more';
    }

    $alignClass = [
        'left' => 'text-left',
        'center' => 'text-center',
        'right' => 'text-right'
    ][$align] ?? 'text-left';

    $justifyClass = [
        'left' => 'justify-start',
        'center' => 'justify-center',
        'right' => 'justify-end'
    ][$align] ?? 'justify-start';
@endphp

<th {{ $attributes->merge(['class' => 'px-6 py-4 font-label-caps text-slate-500 uppercase text-[10px] tracking-widest whitespace-nowrap']) }}>
    <a href="{{ request()->fullUrlWithQuery(['sort_field' => $field, 'sort_dir' => $nextDir]) }}" 
       class="flex items-center gap-1 hover:text-primary transition-colors {{ $justifyClass }}">
        <span>{{ $label }}</span>
        <span class="material-symbols-outlined text-[16px] {{ $isSorted ? 'text-primary' : 'text-slate-300' }}">
            {{ $icon }}
        </span>
    </a>
</th>
