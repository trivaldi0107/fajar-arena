@props(['name', 'default' => '', 'options' => [], 'placeholder' => 'Pilih...'])
<div x-data="{ 
        open: false, 
        selected: '{{ old($name, $default) }}',
        options: {{ json_encode($options) }}
    }"
    @click.away="open = false" 
    class="relative w-full"
>
    <input type="hidden" name="{{ $name }}" :value="selected"><button type="button" @click="open = !open" class="w-full m-0 flex items-center justify-between border border-gray-200 bg-gray-50 hover:bg-white px-4 py-3 rounded-xl focus:ring-2 focus:ring-blue-500 outline-none transition-all text-sm cursor-pointer font-medium text-gray-800">
        <span x-text="options.find(o => o.value == selected)?.label || '{{ $placeholder }}'"></span>
        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="#9ca3af" class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''">
            <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
        </svg>
    </button>
    <div x-show="open" 
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 translate-y-2"
        x-transition:enter-end="opacity-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 translate-y-0"
        x-transition:leave-end="opacity-0 translate-y-2"
        class="absolute left-0 mt-2 w-full bg-white border border-gray-100 rounded-2xl shadow-xl overflow-hidden py-2 max-h-60 overflow-y-auto z-[60]"
        style="display: none;"
    >
        <template x-for="option in options" :key="option.value">
            <button type="button" 
                @click="selected = String(option.value); open = false"
                class="w-full text-left px-4 py-2.5 text-sm transition-all duration-200 font-medium"
                :class="selected == String(option.value) ? 'bg-blue-50 text-blue-700' : 'text-gray-600 hover:bg-gray-50 hover:text-gray-900'"
            >
                <span x-text="option.label"></span>
            </button>
        </template>
    </div>
</div>
