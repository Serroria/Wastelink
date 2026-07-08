@props([
    'href' => '#',
    'title' => '',
    'description' => '',
    'icon' => 'recycle',
    'isButton' => false,
    'onclick' => '',
    'badge' => '',
    'color' => 'emerald'
])

@php
    // Define color themes based on the selected prop
    $theme = [
        'emerald' => [
            'iconColor' => 'text-emerald-700',
            'hoverBorder' => 'hover:border-emerald-500/30',
            'hoverBg' => 'hover:bg-emerald-50/10',
            'badgeBg' => 'bg-emerald-50 text-emerald-800 border-emerald-100',
            'accentLine' => 'bg-emerald-500/10'
        ],
        'amber' => [
            'iconColor' => 'text-amber-700',
            'hoverBorder' => 'hover:border-amber-500/30',
            'hoverBg' => 'hover:bg-amber-50/10',
            'badgeBg' => 'bg-amber-50 text-amber-800 border-amber-100',
            'accentLine' => 'bg-amber-500/10'
        ],
        'sky' => [
            'iconColor' => 'text-sky-700',
            'hoverBorder' => 'hover:border-sky-500/30',
            'hoverBg' => 'hover:bg-sky-50/10',
            'badgeBg' => 'bg-sky-50 text-sky-800 border-sky-100',
            'accentLine' => 'bg-sky-500/10'
        ],
        'indigo' => [
            'iconColor' => 'text-indigo-700',
            'hoverBorder' => 'hover:border-indigo-500/30',
            'hoverBg' => 'hover:bg-indigo-50/10',
            'badgeBg' => 'bg-indigo-50 text-indigo-800 border-indigo-100',
            'accentLine' => 'bg-indigo-500/10'
        ],
    ][$color] ?? [
        'iconColor' => 'text-emerald-700',
        'hoverBorder' => 'hover:border-emerald-500/30',
        'hoverBg' => 'hover:bg-emerald-50/10',
        'badgeBg' => 'bg-emerald-50 text-emerald-800 border-emerald-100',
        'accentLine' => 'bg-emerald-500/10'
    ];
@endphp

@if($isButton)
    <button onclick="{{ $onclick }}" class="group flex items-center justify-between p-4 bg-white border border-stone-100 rounded-2xl text-left w-full transition-all duration-300 {{ $theme['hoverBorder'] }} {{ $theme['hoverBg'] }} hover:shadow-[0_8px_30px_rgb(0,0,0,0.02)] focus:outline-none cursor-pointer">
@else
    <a href="{{ $href }}" class="group flex items-center justify-between p-4 bg-white border border-stone-100 rounded-2xl text-left w-full transition-all duration-300 {{ $theme['hoverBorder'] }} {{ $theme['hoverBg'] }} hover:shadow-[0_8px_30px_rgb(0,0,0,0.02)] cursor-pointer">
@endif
        <div class="flex items-center gap-4 min-w-0">
            <!-- Handcrafted Custom Icon Styling (No Pastel Box) -->
            <div class="relative w-12 h-12 flex items-center justify-center shrink-0">
                <!-- Decorative organic soft circle behind the icon -->
                <div class="absolute inset-0 rounded-full scale-[0.8] group-hover:scale-[1.15] transition-transform duration-300 {{ $theme['accentLine'] }}"></div>
                <i class="bi bi-{{ $icon }} text-2xl relative z-10 transition-transform duration-300 group-hover:scale-110 group-hover:rotate-6 {{ $theme['iconColor'] }}"></i>
            </div>

            <div class="min-w-0">
                <div class="flex items-center gap-2">
                    <span class="text-sm font-bold text-slate-800 group-hover:text-emerald-800 transition-colors font-display leading-tight">{{ $title }}</span>
                    @if($badge)
                        <span class="text-[9px] font-bold px-1.5 py-0.5 rounded-full border {{ $theme['badgeBg'] }} tracking-wide uppercase">{{ $badge }}</span>
                    @endif
                </div>
                <p class="text-xs text-slate-400 mt-1 truncate leading-none">{{ $description }}</p>
            </div>
        </div>

        <!-- Elegant Arrow Indicator -->
        <div class="w-7 h-7 rounded-full bg-slate-50 flex items-center justify-center text-slate-400 group-hover:bg-emerald-600 group-hover:text-white transition-all shrink-0 ml-2 shadow-sm">
            <i class="bi bi-arrow-right text-xs transform group-hover:translate-x-0.5 transition-transform"></i>
        </div>
@if($isButton)
    </button>
@else
    </a>
@endif
