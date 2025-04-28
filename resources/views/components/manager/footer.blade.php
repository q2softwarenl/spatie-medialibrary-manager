@props(['count', 'size'])

<div class="relative flex items-center justify-between h-12 py-2 px-4 text-base-content/75 dark:bg-transparent text-xs">
    @if(count($errors))
    <div class="absolute bg-error left-2 right-2 bottom-2 py-2 px-4 rounded-md border border-error text-white text-sm flex items-center gap-4">
        <div>
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" class="w-5 h-5">
                <path fill-rule="evenodd" d="M18 10a8 8 0 1 1-16 0 8 8 0 0 1 16 0Zm-8-5a.75.75 0 0 1 .75.75v4.5a.75.75 0 0 1-1.5 0v-4.5A.75.75 0 0 1 10 5Zm0 10a1 1 0 1 0 0-2 1 1 0 0 0 0 2Z" clip-rule="evenodd" />
            </svg>
        </div>
        <div>
            @foreach($errors->all() as $error)
            <p>{{ $error }}</p>
            @endforeach
        </div>
    </div>
    @endif
    
    <div class="flex items-center gap-3" x-show="canOverview" style="display:none">
        <div class="mt-[2px]">
            <button x-on:click="navigateToCollection(null)" class="text-base-content/75 hover:text-base-content group">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4"><path d="M3 3.5A1.5 1.5 0 0 1 4.5 2h1.879a1.5 1.5 0 0 1 1.06.44l1.122 1.12A1.5 1.5 0 0 0 9.62 4H11.5A1.5 1.5 0 0 1 13 5.5v1H3v-3ZM3.081 8a1.5 1.5 0 0 0-1.423 1.974l1 3A1.5 1.5 0 0 0 4.081 14h7.838a1.5 1.5 0 0 0 1.423-1.026l1-3A1.5 1.5 0 0 0 12.919 8H3.081Z" /></svg>
            </button>
        </div>
        <div class="text-base-content/75 select-none" x-show="activeCollection" style="display: none;">/</div>
        <div x-text="activeCollectionLabel"></div>
    </div>
    <div>
        <progress x-show="isUploading" class="smm-progress progress w-56" :value="progress" max="100" style="display:none;"></progress>
    </div>
    <div class="space-x-1">
        @if ($size > 0)
        <span>
            <span class="group">
                <span class="group-hover:hidden inline" x-text="totalFileSizeMb"></span>
                <span class="hidden group-hover:inline" x-text="totalFileSize"></span>
            </span>
        </span>
        <span class="select-none">&middot;</span>
        @endif
        <span x-text="totalFilesCount"></span>
    </div>
</div>