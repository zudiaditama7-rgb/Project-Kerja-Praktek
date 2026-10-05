@props(['label', 'value', 'icon', 'color' => 'blue'])

@php
$colors = [
    'blue' => 'bg-blue-50 dark:bg-blue-500/10 text-blue-600 dark:text-blue-400',
    'green' => 'bg-emerald-100 dark:bg-emerald-500/20 text-[#065F46] dark:text-emerald-400',
    'emerald' => 'bg-emerald-100 dark:bg-emerald-500/20 text-[#065F46] dark:text-emerald-400',
    'yellow' => 'bg-yellow-50 dark:bg-yellow-500/10 text-yellow-600 dark:text-yellow-400',
    'red' => 'bg-red-50 dark:bg-red-500/10 text-red-600 dark:text-red-400',
    'indigo' => 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400',
];
$iconColor = $colors[$color] ?? $colors['blue'];
@endphp

<div class="bg-white dark:bg-[#0f172a] rounded-2xl p-6 shadow-md hover:shadow-xl premium-card border border-gray-50 dark:border-white/5 flex justify-between items-center group transition-shadow">
    <div>
        <p class="text-sm font-medium text-gray-500 mb-1 group-hover:text-gray-700 transition-colors">{{ $label }}</p>
        <p class="text-2xl font-bold text-gray-900">{{ $value }}</p>
    </div>
    <div class="{{ $iconColor }} p-4 rounded-2xl transition-transform duration-300 group-hover:scale-110">
        <i data-lucide="{{ $icon }}" class="w-6 h-6"></i>
    </div>
</div>
