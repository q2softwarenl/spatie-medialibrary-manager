@props(['label'])

<div class="shrink-0 border-b px-4 py-2 flex gap-2 justify-between items-center">
    <div class="min-w-0 flex gap-4 items-center">
        <button
            x-on:click="navigateToHome()"
            role="button" 
            type="button"
            class="smm-btn"
            x-show="canOverview"
            style="display:none"
        >
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M12.5 9.75A2.75 2.75 0 0 0 9.75 7H4.56l2.22 2.22a.75.75 0 1 1-1.06 1.06l-3.5-3.5a.75.75 0 0 1 0-1.06l3.5-3.5a.75.75 0 0 1 1.06 1.06L4.56 5.5h5.19a4.25 4.25 0 0 1 0 8.5h-1a.75.75 0 0 1 0-1.5h1a2.75 2.75 0 0 0 2.75-2.75Z" clip-rule="evenodd" /></svg>
            <span class="hidden sm:inline">{{ __('Back') }}</span>
        </button>
        <div class="truncate text-sm font-bold leading-6">{{ $label }}</div>
    </div>

    @isset($slot)
    <div class="shrink-0 flex items-center justify-end gap-1">
        {{$slot}}
    </div>
    @endisset
</div>