<div
    id="{{ $managerKey }}"
    class="filemanager relative h-full"
    wire:loading.delay.class="pointer-events-none opacity-50"
    x-data="spatie_medialibrary_manager(@this)"
    x-on:livewire-upload-start="isUploading = true"
    x-on:livewire-upload-finish="isUploading = false"
    x-on:livewire-upload-error="isUploading = false"
    x-on:livewire-upload-progress="progress = $event.detail.progress"
>
    {{-- Collection folder buttons --}}
    <div class="p-4 pb-2 flex flex-wrap content-start justify-center sm:justify-start gap-4 {{ $innerClass }}" style="display:none;" x-show="!activeCollection">
        <template x-for="(collection, index) in allMediaCollections" :key="'collection_button_' + collection.collection_name">
            <button
                x-data="{ droppingFile: false }"
                class="cursor-pointer h-28 w-28 flex items-start justify-center rounded-field ring-inset ring-base-300 dark:ring-base-50 transition hover:ring-4 border overflow-hidden"
                :class="droppingFile ? 'outline-dashed outline-offset-2 outline-4 outline-base-300 dark:outline-base-50' : ''"
                x-on:click="navigateToCollection(collection)"
                x-on:drop="droppingFile = false"
                x-on:drop.prevent="handleFileDrop($event, collection)"
                x-on:dragover.prevent="collection.canUpload === true ? droppingFile = true : false"
                x-on:dragleave.prevent="droppingFile = false"
            >
                <div class="flex flex-col items-center justify-center mt-4 w-full">                            
                    <div class="text-base-content/75 relative" x-show="collection.singleFile && collection.count === 0">                                
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" /></svg>
                    </div>
                    <div class="text-base-content/75 relative" x-show="collection.singleFile && collection.count > 0">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-12"><path fill-rule="evenodd" d="M9 1.5H5.625c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0 0 16.5 9h-1.875a1.875 1.875 0 0 1-1.875-1.875V5.25A3.75 3.75 0 0 0 9 1.5Zm6.61 10.936a.75.75 0 1 0-1.22-.872l-3.236 4.53L9.53 14.47a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.14-.094l3.75-5.25Z" clip-rule="evenodd" /><path d="M12.971 1.816A5.23 5.23 0 0 1 14.25 5.25v1.875c0 .207.168.375.375.375H16.5a5.23 5.23 0 0 1 3.434 1.279 9.768 9.768 0 0 0-6.963-6.963Z" /></svg>
                    </div>
                    <div class="text-yellow-400 dark:text-yellow-300 relative" x-show="!collection.singleFile && collection.count === 0">                                
                        <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-12"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 0 1 4.5 9.75h15A2.25 2.25 0 0 1 21.75 12v.75m-8.69-6.44-2.12-2.12a1.5 1.5 0 0 0-1.061-.44H4.5A2.25 2.25 0 0 0 2.25 6v12a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9a2.25 2.25 0 0 0-2.25-2.25h-5.379a1.5 1.5 0 0 1-1.06-.44Z" /></svg>
                    </div>
                    <div class="text-yellow-400 dark:text-yellow-300 relative" x-show="!collection.singleFile && collection.count > 0" style="display:none">                                
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="size-12"><path d="M19.5 21a3 3 0 0 0 3-3v-4.5a3 3 0 0 0-3-3h-15a3 3 0 0 0-3 3V18a3 3 0 0 0 3 3h15ZM1.5 10.146V6a3 3 0 0 1 3-3h5.379a2.25 2.25 0 0 1 1.59.659l2.122 2.121c.14.141.331.22.53.22H19.5a3 3 0 0 1 3 3v1.146A4.483 4.483 0 0 0 19.5 9h-15a4.483 4.483 0 0 0-3 1.146Z" /></svg>
                        <span class="absolute text-yellow-900 text-xs font-black top-[18px] px-2 h-7 w-12 flex items-center justify-center"><span class="truncate" x-text="collection.count"></span></span>
                    </div>

                    <div class="h-10 flex items-center justify-center w-full overflow-hidden">
                        <div class="px-2 text-xs leading-3 overflow-hidden text-ellipsis" style="hyphens: auto;" x-text="collection.label"></div>
                    </div>
                </div>
            </button>
        </template>

        <div style="display:none;" x-show="allMediaCollections.length === 0">
            {{ __('This object doesn\'t have any collections.') }}
        </div>
    </div>

    {{-- Collection folder inner --}}
    <div style="display:none;" x-show="activeCollection">

        <template x-for="(collection, index) in allMediaCollections" :key="'collection_' + collection.collection_name">
            <div 
                x-data="{ droppingFileFolder: false }"
                style="display:none;" 
                class="flex flex-col gap-0 {{ $innerClass }}" 
                x-show="activeCollection === collection.collection_name"
                x-on:drop="droppingFileFolder = false"
                x-on:drop.prevent="handleFileDrop($event, collection)"
                x-on:dragover.prevent="collection.canUpload === true ? droppingFileFolder = true : false"
                x-on:dragleave.prevent="droppingFileFolder = false"
            >
                <x-spatie-medialibrary-manager::manager.header>
                    <x-slot:label><span x-text="collection.label"></span></x-slot:label>
                    
                    <button 
                        class="smm-btn-ghost" 
                        x-show="collection.canDownloadAll" 
                        style="display:none" 
                        x-on:click="downloadAll(collection)"
                        :disabled="allMediaItems.filter(item => collection.collection_name === item.collection_name).length === 0"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M4.5 13a3.5 3.5 0 0 1-1.41-6.705A3.5 3.5 0 0 1 9.72 4.124a2.5 2.5 0 0 1 3.197 3.018A3.001 3.001 0 0 1 12 13H4.5Zm6.28-3.97a.75.75 0 1 0-1.06-1.06l-.97.97V6.25a.75.75 0 0 0-1.5 0v2.69l-.97-.97a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.06 0l2.25-2.25Z" clip-rule="evenodd" /></svg>
                        {{__('Download All')}}
                    </button>
                    <label 
                        class="smm-btn-ghost" 
                        x-show="canUpload" 
                        style="display:none" 
                        :disabled="collection.singleFile && allMediaItems.filter(item => collection.collection_name === item.collection_name).length === 1"
                    >
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M4.5 13a3.5 3.5 0 0 1-1.41-6.705A3.5 3.5 0 0 1 9.72 4.124a2.5 2.5 0 0 1 3.197 3.018A3.001 3.001 0 0 1 12 13H4.5Zm.72-5.03a.75.75 0 0 0 1.06 1.06l.97-.97v2.69a.75.75 0 0 0 1.5 0V8.06l.97.97a.75.75 0 1 0 1.06-1.06L8.53 5.72a.75.75 0 0 0-1.06 0L5.22 7.97Z" clip-rule="evenodd" /></svg>
                        <span class="hidden sm:inline" x-show="collection.singleFile" style="display:none;">{{ __('Choose file') }}</span>
                        <span class="hidden sm:inline" x-show="!collection.singleFile" style="display:none;">{{ __('Choose files') }}</span>
                        <input 
                            type="file" 
                            x-on:change="handleFileUpload($event, collection)"
                            class="sr-only" 
                            multiple
                            accept="{{ $accept }}"
                        >
                    </label>
                </x-spatie-medialibrary-manager::manager.header>

                <div 
                    x-show="allMediaItems.filter(item => collection.collection_name === item.collection_name).length >= 0"
                    style="display:none"
                    :class="droppingFileFolder ? 'outline-dashed -outline-offset-4 outline-4 outline-base-300 dark:outline-base-50' : 'outline-4 outline-transparent'"
                    class="grow min-h-20 {{ $innerClass }}"
                >
                    <div 
                        x-text="collection.empty_text"
                        x-show="allMediaItems.filter(item => collection.collection_name === item.collection_name).length === 0"
                        style="display:none"
                        class="w-full py-8 flex flex-col items-center justify-center text-base-content/75 text-center"
                    ></div>
                    
                    {{-- File --}}
                    <template x-for="(mediaItem, index) in allMediaItems.filter(item => collection.collection_name === item.collection_name).filter(item => !item.deleted)" :key="'file_' + mediaItem.id">
                        <div>
                            <div class="py-1 px-4 flex justify-between items-center gap-4">
                                <div class="grow min-w-0 flex gap-4 items-center">
                                    <button 
                                        class="shrink-0 relative rounded overflow-hidden border cursor-zoom-in"
                                        :class="(['image', 'pdf'].includes(mediaItem.type) ? 'border-base dark:border-base-content bg-base-300 dark:bg-base-50' : 'p-2 border-transparent')"
                                        :style="'background-color: ' + mediaItem.theme_color"
                                        x-show="mediaItem.thumbnail_url" 
                                        style="display:none;" 
                                        x-on:click="previewThumb(mediaItem)"
                                    >
                                        <img 
                                            :src="mediaItem.thumbnail_url ? mediaItem.thumbnail_url : ''" 
                                            :alt="mediaItem.name" 
                                            class="object-center"
                                            :class="['image', 'pdf'].includes(mediaItem.type) ? 'size-8 object-cover' : 'size-4 object-contain invert'"
                                        />
                                    </button>

                                    <div class="p-2 rounded bg-base-300 dark:bg-base-50" x-show="!mediaItem.thumbnail_url" style="display:none;">
                                        <x-spatie-medialibrary-manager::icons.loading class="animate-spin size-4" />
                                    </div>

                                    <div class="group text-sm max-w-14 md:max-w-64 lg:max-w-xs xl:max-w-xl 2xl:max-w-2xl truncate" x-on:dblclick="editMediaItem(mediaItem, collection)" x-show="!mediaItem.editing">
                                        <span x-text="mediaItem.name"></span>
                                        <span class="hidden group-hover:inline text-xs ml-1 text-base-content/75" x-text="mediaItem.file_name"></span>
                                    </div>

                                    <div class="w-full flex gap-1" x-show="mediaItem.editing === true" style="display:none">
                                        <input 
                                            type="text" 
                                            class="w-full input input-sm input-bordered" 
                                            x-model="mediaItem.updatingName"
                                            @keyup.enter="updateMediaItem(mediaItem, collection)"
                                            @keyup.esc="cancelEditing(mediaItem)"
                                        />
                                        <button class="smm-btn-primary" x-on:click="updateMediaItem(mediaItem, collection)">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M12.416 3.376a.75.75 0 0 1 .208 1.04l-5 7.5a.75.75 0 0 1-1.154.114l-3-3a.75.75 0 0 1 1.06-1.06l2.353 2.353 4.493-6.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" /></svg>
                                            <span class="hidden md:inline">{{__('Save')}}</span>
                                        </button>
                                        <button class="smm-btn-ghost" x-on:click="cancelEditing(mediaItem)">
                                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4"><path d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z" /></svg>
                                            <span class="hidden md:inline">{{__('Cancel')}}</span>
                                        </button>
                                    </div>
                                </div>

                                <div class="group gap-2 items-center shrink-0 whitespace-nowrap" x-show="!mediaItem.editing">
                                    <span class="text-xs text-base-content/75">
                                        <span class="group-hover:hidden inline" x-text="mediaItem.date"></span>
                                        <span class="hidden group-hover:inline" x-text="mediaItem.datetime"></span>
                                    </span>
                                    <span class="select-none text-base-content/75">&middot;</span>
                                    <span class="text-xs text-base-content/75">
                                        <span class="group-hover:hidden inline" x-text="mediaItem.filesize"></span>
                                        <span class="hidden group-hover:inline" x-text="mediaItem.filesizemb"></span>
                                    </span>
                                </div>

                                <div 
                                    class="relative flex justify-end shrink-0"
                                    x-show="collection.canDownload || collection.canEdit || (canMove && collection.canMove) || collection.canDelete"
                                    style="display:none"
                                >
                                    <button
                                        type="button"
                                        class="smm-btn-ghost-square"
                                        title="{{ __('Download file') }}"
                                        x-on:click.prevent="downloadMediaItem(mediaItem, collection)"
                                        x-bind:disabled="isMoving || isEditing"
                                        x-show="collection.canDownload"
                                        style="display:none"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4"><path d="M8.75 2.75a.75.75 0 0 0-1.5 0v5.69L5.03 6.22a.75.75 0 0 0-1.06 1.06l3.5 3.5a.75.75 0 0 0 1.06 0l3.5-3.5a.75.75 0 0 0-1.06-1.06L8.75 8.44V2.75Z" /><path d="M3.5 9.75a.75.75 0 0 0-1.5 0v1.5A2.75 2.75 0 0 0 4.75 14h6.5A2.75 2.75 0 0 0 14 11.25v-1.5a.75.75 0 0 0-1.5 0v1.5c0 .69-.56 1.25-1.25 1.25h-6.5c-.69 0-1.25-.56-1.25-1.25v-1.5Z" /></svg>
                                    </button>
                                    <button
                                        type="button"
                                        class="smm-btn-ghost-square"
                                        title="{{ __('Rename file') }}"
                                        x-on:click.prevent="editMediaItem(mediaItem, collection)"
                                        x-bind:disabled="isMoving || isEditing"
                                        x-show="collection.canEdit"
                                        style="display:none"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M11.013 2.513a1.75 1.75 0 0 1 2.475 2.474L6.226 12.25a2.751 2.751 0 0 1-.892.596l-2.047.848a.75.75 0 0 1-.98-.98l.848-2.047a2.75 2.75 0 0 1 .596-.892l7.262-7.261Z" clip-rule="evenodd" /></svg>
                                    </button>
                                    <button
                                        type="button"
                                        class="smm-btn-ghost-square"
                                        title="{{ __('Move file') }}"
                                        x-on:click.prevent="movingMediaItem(mediaItem, collection)"
                                        x-bind:disabled="isMoving || isEditing"
                                        x-show="canMove && collection.canMove"
                                        style="display:none"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" width="24" height="24" fill="currentColor" class="size-4"><path d="M2 4.75C2 3.784 2.784 3 3.75 3h4.965a1.75 1.75 0 0 1 1.456.78l1.406 2.109a.25.25 0 0 0 .208.111h8.465c.966 0 1.75.784 1.75 1.75v11.5A1.75 1.75 0 0 1 20.25 21H3.75A1.75 1.75 0 0 1 2 19.25Zm12.78 4.97a.749.749 0 0 0-1.275.326.749.749 0 0 0 .215.734l1.72 1.72H6.75a.75.75 0 0 0 0 1.5h8.69l-1.72 1.72a.749.749 0 0 0 .326 1.275.749.749 0 0 0 .734-.215l3-3a.75.75 0 0 0 0-1.06Z"></path></svg>
                                    </button>
                                    <button
                                        type="button"
                                        class="smm-btn-ghost-error-square"
                                        title="{{ __('Delete file') }}"
                                        x-on:click.prevent="deleteMediaItem(managerKey, mediaItem, collection, '{{ __('Are you sure to delete this file? You cannot undo this action.') }}');"
                                        x-bind:disabled="isMoving || isEditing"
                                        x-show="collection.canDelete"
                                        style="display:none"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M5 3.25V4H2.75a.75.75 0 0 0 0 1.5h.3l.815 8.15A1.5 1.5 0 0 0 5.357 15h5.285a1.5 1.5 0 0 0 1.493-1.35l.815-8.15h.3a.75.75 0 0 0 0-1.5H11v-.75A2.25 2.25 0 0 0 8.75 1h-1.5A2.25 2.25 0 0 0 5 3.25Zm2.25-.75a.75.75 0 0 0-.75.75V4h3v-.75a.75.75 0 0 0-.75-.75h-1.5ZM6.05 6a.75.75 0 0 1 .787.713l.275 5.5a.75.75 0 0 1-1.498.075l-.275-5.5A.75.75 0 0 1 6.05 6Zm3.9 0a.75.75 0 0 1 .712.787l-.275 5.5a.75.75 0 0 1-1.498-.075l.275-5.5a.75.75 0 0 1 .786-.711Z" clip-rule="evenodd" /></svg>
                                    </button>
                                </div>
                            </div>
                            <div class="mb-2 border-y bg-base-200 dark:bg-base-50 px-4 py-2" x-show="mediaItem.moving" style="display:none;">
                                <strong class="block text-sm mb-2">{{ __('Where do you want to move the file?') }}</strong>
                                <div class="flex flex-col items-start mb-2">
                                    <template x-for="(moveToCollection, index) in mediaItem.moveToMediaCollectionOptions" :key="'moveToCollection_' + moveToCollection.value">
                                        <div>
                                            <label class="label cursor-pointer gap-2">
                                                <input type="radio" class="radio" x-model="mediaItem.moveToMediaCollectionName" :value="moveToCollection.value" />
                                                <span class="label-text" x-text="moveToCollection.label"></span>
                                            </label>
                                            
                                        </div>
                                    </template>
                                </div>
                                <div class="flex flex-wrap items-center gap-1 justify-end">
                                    <button 
                                        class="smm-btn-ghost" 
                                        x-on:click="cancelMoving(mediaItem)"
                                    >{{ __('Cancel') }}</button>
                                    <button 
                                        class="smm-btn-primary" 
                                        x-bind:disabled="mediaItem.moveToMediaCollectionName === undefined" 
                                        x-on:click="moveMediaItem(mediaItem, collection)"
                                    >
                                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4"><path fill-rule="evenodd" d="M12.416 3.376a.75.75 0 0 1 .208 1.04l-5 7.5a.75.75 0 0 1-1.154.114l-3-3a.75.75 0 0 1 1.06-1.06l2.353 2.353 4.493-6.74a.75.75 0 0 1 1.04-.207Z" clip-rule="evenodd" /></svg>
                                        {{__('Move to collection')}}
                                    </button>
                                </div>

                            </div>
                        </div>
                    </template>

                </div>
            </div>
        </template>

    </div>

    <x-spatie-medialibrary-manager::manager.footer :count="$_global_count" :size="$_global_size" />

    <div
        x-cloak 
        x-show="previewUrl" 
        class="w-full rounded-b-xl overflow-hidden"
    >
        <div class="border-t flex justify-end p-2">
            <button class="smm-btn-ghost-square" x-on:click="closeThumb()">
                <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 16 16" fill="currentColor" class="size-4">
                    <path d="M5.28 4.22a.75.75 0 0 0-1.06 1.06L6.94 8l-2.72 2.72a.75.75 0 1 0 1.06 1.06L8 9.06l2.72 2.72a.75.75 0 1 0 1.06-1.06L9.06 8l2.72-2.72a.75.75 0 0 0-1.06-1.06L8 6.94 5.28 4.22Z" />
                </svg>
            </button>
        </div>
        <div class="relative max-h-[500px] flex">
            <iframe 
                allowfullscreen
                :src="previewUrl"
                class="grow"
                :height="previewHeight"
            ></iframe>
        </div>
    </div>
</div>