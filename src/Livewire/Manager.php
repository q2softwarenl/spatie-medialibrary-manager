<?php

namespace Q2softwarenl\SpatieMedialibraryManager\Livewire;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rules\File as FileRules;
use Illuminate\View\View;
use Illuminate\Support\Str;
use Livewire\Attributes\Locked;
use Livewire\Attributes\On;
use Livewire\Attributes\Validate;
use Livewire\Component;
use Livewire\WithFileUploads;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Q2softwarenl\SpatieMedialibraryManager\ManagerFile;
use Spatie\MediaLibrary\Support\MediaStream;
use Spatie\MediaLibrary\MediaCollections\MediaCollection;
use Livewire\Attributes\Url;

class Manager extends Component
{
    use WithFileUploads;
    
    public $innerClass = '';
    
    #[Locked]
    public string $managerKey;
    #[Locked]
    private string $table;

    public int $maxFileSizeBytes = 0;

    public bool $canOverview = true;
    public bool $canUpload = true;
    public bool $canDownloadAll = true;
    
    public bool $canDownload = false;
    public bool $canEdit = true;
    public bool $canMove = false;
    public bool $canDelete = true;

    public string $accept = '';

    #[Locked]
    public array $allMediaCollections = [];

    public array $allMediaItems = [];

    #[Locked]
    public string $totalFilesCount = '0 files';

    #[Locked]
    public string $totalFileSizeMb = '0 MB';

    #[Locked]
    public string $totalFileSize = '0 kB';

    public $rawFiles = [];
    public string|null $uploadingToMediaCollection = null;

    // #[Url(except: '', as: 'folder')] // FIXME: this doesnt work with multiple managers in one view
    public ?string $activeCollection;

    public Model $model;

    protected $listeners = [
        'addMediaItemToCollection' => 'addMediaItemToCollection',
        'removeMediaItemFromCollection' => 'removeMediaItemFromCollection',
    ];

    public function mount(
        Model $model,
        ?array $whereInMediaCollections = null,
        $conversion = null,
    ) {
        $this->managerKey = 'manager_'. Str::random(32);
        $this->table = $model->getTable();

        if (!($model instanceof HasMedia))
            throw new Exception('The ' . get_class($model) . ' class must implement Spatie\MediaLibrary\HasMedia.');
        
        if ($conversion && !in_array(InteractsWithMedia::class, class_uses_recursive($model)))
            throw new Exception('The ' . get_class($model) . ' class must use Spatie\MediaLibrary\InteractsWithMedia.');

        if(!$whereInMediaCollections)
            $this->canOverview = true;

        $this->allMediaCollections = $model
            ->getRegisteredMediaCollections()
            ->filter(function($collection) use ($whereInMediaCollections) {
                return is_null($whereInMediaCollections) || in_array($collection->name, $whereInMediaCollections);
            })
            ->mapWithKeys(function($collection) use ($model) {
                $collection_name = $collection->name;
                $mediaItems = $model->media->where('collection_name', $collection_name)->values();
                $canUpload = $this->canUpload && auth()->user()->can('spatieMedialibraryManagerUploadMedia', [$model]);

                return [
                    $collection_name => [
                        'collection_name' => $collection_name,
                        'label' => __('mediaCollections.' . $this->table . '.' . $collection_name),
                        'empty_text' => $canUpload ? trans_choice('smm::texts.upload_file_instructions', ($collection->singleFile ? 1 : 2)) : __('smm::texts.no_files_in_collection', ['collection' => __('mediaCollections.' . $this->table . '.' . $collection_name)]),
                        'singleFile' => $collection->singleFile,
                        'count' => $mediaItems->count(),
                        'size' => collect($mediaItems)->sum('size'),
                        'canDownload' => $this->canDownload && auth()->user()->can('spatieMedialibraryManagerDownloadMedia', [$model]),
                        'canDownloadAll' => $this->canDownloadAll && !$collection->singleFile && auth()->user()->can('spatieMedialibraryManagerDownloadAllMedia', [$model, $collection_name]),
                        'canUpload' => $canUpload && auth()->user()->can('spatieMedialibraryManagerUploadMedia', [$model]),
                        'canEdit' => $this->canEdit && auth()->user()->can('spatieMedialibraryManagerEditMedia', [$model]),
                        'canMove' => $this->canMove && auth()->user()->can('spatieMedialibraryManagerMoveMedia', [$model]),
                        'canDelete' => $this->canDelete && auth()->user()->can('spatieMedialibraryManagerDeleteMedia', [$model]),
                    ]
                ];
            })->toArray();
        
        $this->allMediaItems = $model->media->map(fn($mediaItem) => ManagerFile::fromModel($mediaItem)->toArray())->toArray();
        
        // If manager is displaying just one collection, set activeCollection and change some permissions
        if(count($this->allMediaCollections) === 1) {
            $this->activeCollection = array_key_first($this->allMediaCollections);
            $this->canOverview = false;
            $this->canMove = false;
        }

        $this->maxFileSizeBytes = config('media-library.max_file_size');
        
        $this->setTotalsFooter();
        
        $this->model = $model;
    }

    /**
     * File upload
     */
    public function updatedRawFiles($value): void
    {
        abort_unless(!empty($this->uploadingToMediaCollection) && isset($this->allMediaCollections[$this->uploadingToMediaCollection]), 404, 'Collection is required.');

        // TODO: if (!$this->allMediaCollections[$this->uploadingToMediaCollection]['can']['upload']) {
        //     $this->addError('rawFiles.*', __('smm::texts.policy_denies_upload_to_collection', ['collection' => $this->allMediaCollections[$this->uploadingToMediaCollection]['label']]));
        //     return;
        // }
        
        $mediaCollection = $this->model->getRegisteredMediaCollections()->where('name', $this->uploadingToMediaCollection)->first();
        
        if ($mediaCollection->singleFile && count($value) > 1) {
            $this->addError('rawFiles.*', __('smm::texts.only_one_file_can_be_uploaded', ['collection' => $this->allMediaCollections[$this->uploadingToMediaCollection]['label']]));
            return;
        }
        
        $validator = $this->validate([
            'rawFiles' => 'required|array',
            'rawFiles.*' => [
                'required',
                FileRules::types($this->accept)->max($this->maxFileSizeBytes)
            ],
        ]);

        $rawValidatedFiles = $validator['rawFiles'];

        // if ($mediaCollection->collectionTotalFileSizeLimit ?? false) {
        //     $collectionSize = $this->allMediaCollections[$this->uploadingToMediaCollection]['size'];
        //     $sizeToUpload = collect($rawValidatedFiles)->sum(function($item) { return $item->getSize(); });

        //     if($mediaCollection->collectionTotalFileSizeLimit < ($collectionSize + $sizeToUpload)) {
        //         $this->addError('collections', __('You may upload a maximum of :max_size to this collection. You are trying to upload :filesize, exceeding the limit by :exceeded.', [
        //             'max_size' => ceil($mediaCollection->collectionTotalFileSizeLimit / 1024) . 'KB', 
        //             'filesize' => ceil($sizeToUpload / 1024) . 'KB', 
        //             'exceeded' => ceil(($collectionSize + $sizeToUpload - $mediaCollection->collectionTotalFileSizeLimit) / 1024) . 'KB', 
        //         ]));
        //         return;
        //     }
        //     // dd(, $rawValidatedFiles, collect($rawValidatedFiles)->sum(function($item) { return $item->getSize(); }), $mediaCollection->collectionTotalFileSizeLimit);
        //     // $this->addError('rawFiles.*', __('smm::texts.only_one_file_can_be_uploaded', ['collection' => $this->allMediaCollections[$this->uploadingToMediaCollection]['label']]));
        // }

        foreach ($rawValidatedFiles as $file) {

            try {
                // First check if file has been uploaded before (latest)
                $duplicateCount = $this->model->getMedia($this->uploadingToMediaCollection)->where('file_name', $file->getClientOriginalName())->sortByDesc('id')->count();
                $file_name = $file->getClientOriginalName();
                $name = pathinfo($file_name, PATHINFO_FILENAME);
                
                // If file has been uploaded before add a customProperty to the media item
                if ($duplicateCount > 0) {
                    $name = $name . " ($duplicateCount)";
                }

                $addedMedia = $this->model
                    ->addMedia($file->path())
                    ->usingName($name)
                    ->usingFileName($file_name)
                    ->toMediaCollection($this->uploadingToMediaCollection);

                $this->dispatch('addMediaItemToCollection', $this->model->id, ManagerFile::fromModel($addedMedia))->to(Manager::class);

            } catch (\Spatie\MediaLibrary\MediaCollections\Exceptions\FileCannotBeAdded $e) {
                $this->addError('files.*', __('smm::texts.file_not_accepted_in_collection', [
                    'file' => $file->getClientOriginalName(),
                    'collection' => $this->allMediaCollections[$this->uploadingToMediaCollection]['label'],
                ]));
            }
        }

        $this->reset('rawFiles');
        $this->reset('uploadingToMediaCollection');
    }

    public function addMediaItemToCollection(int $model_id, array $addedManagerFile)
    {
        if($this->model->id !== $model_id) return;

        if(isset($this->allMediaCollections[$addedManagerFile['collection_name']])) {
            $this->allMediaCollections[$addedManagerFile['collection_name']]['count']++;
            $this->allMediaCollections[$addedManagerFile['collection_name']]['size'] += $addedManagerFile['size'];
            array_push($this->allMediaItems, $addedManagerFile);
        }

        $this->setTotalsFooter();
    }

    public function tryLoadingMissingThumbnails()
    {
        $this->allMediaItems = collect($this->allMediaItems)->map(function($mediaItem) {
            if(!empty($mediaItem['thumbnail_url']))
                return $mediaItem;
            
            return ManagerFile::fromModel(Media::find($mediaItem['id']))->toArray();
        })->toArray();
    }

    public function removeMediaItemFromCollection(int $model_id, int $media_id, $collection_name, int $removed_size)
    {
        if($this->model->id !== $model_id) return;

        if(isset($this->allMediaCollections[$collection_name])) {
            $this->allMediaCollections[$collection_name]['count'] += -1;
            $this->allMediaCollections[$collection_name]['size'] += -$removed_size;
            $this->allMediaItems = collect($this->allMediaItems)
                ->filter(fn($mediaItem) => $mediaItem['id'] !== $media_id)
                ->values()
                ->toArray() ?? [];
        }

        $this->setTotalsFooter();
    }

    public function setTotalsFooter(): void
    {
        $size = collect($this->allMediaCollections)->sum('size');

        $this->totalFilesCount = trans_choice('smm::texts.files', collect($this->allMediaCollections)->sum('count'));
        $this->totalFileSize = number_format($size / 1024, 0, ',', '.') . ' kB';
        $this->totalFileSizeMb = (($size / 1048576) < 1 ? '<1' : number_format($size / 1048576, 0, ',', '.')) . ' MB';
    }

    public function updateMediaItemName($media_id, $value) 
    {
        if(!$this->canEdit) return;

        Media::find($media_id)?->update(['name' => $value]);
    }

    public function deleteMediaItem(string $managerKey, int $media_id) 
    {
        if(!$this->canDelete) return;

        if($this->managerKey === $managerKey) {
            $media = Media::find($media_id)->toArray();

            if($media)
                $this->dispatch('removeMediaItemFromCollection', $this->model->id, $media['id'], $media['collection_name'], $media['size'])->to(Manager::class);
            
            Media::find($media_id)?->delete();
        }
    }

    public function downloadAll(string $collection_name) 
    {
        if(!$this->canDownloadAll) return;

        $medias = $this->model->getMedia($collection_name);            
        
        $downloadName = Str::of(
                preg_replace('/[^A-Za-z0-9\-\_]/', '', $this->allMediaCollections[$collection_name]['label']
            ))
            ->squish()
            ->kebab();

        return MediaStream::create($downloadName . '.zip')->addMedia($medias);   
    }

    public function downloadMediaItem(int $media_id)
    {
        if(!$this->canDownload) return;

        $mediaItem = Media::find($media_id);
        $downloadName = Str::of($mediaItem->name)->finish('.'. pathinfo($mediaItem->file_name, PATHINFO_EXTENSION));

        return response()->download($mediaItem->getPath(), $downloadName);
    }

    public function moveMediaItem(int $media_id, string $to_collection_name)
    {
        if(!$this->canMove) return;
        
        $mediaItem = Media::find($media_id);
        $from_collection_name = $mediaItem->collection_name;
        $mediaItem->collection_name = $to_collection_name;
        $mediaItem->save();

        if(isset($this->allMediaCollections[$from_collection_name])) {
            $this->allMediaCollections[$from_collection_name]['count'] += -1;
            $this->allMediaCollections[$from_collection_name]['size'] += -$mediaItem->size;
        }

        if(isset($this->allMediaCollections[$to_collection_name])) {
            $this->allMediaCollections[$to_collection_name]['count']++;
            $this->allMediaCollections[$to_collection_name]['size'] += $mediaItem->size;
        }
    }

    public function render(): View // after action
    {
        $_global_count = collect($this->allMediaCollections)->sum('count');
        $_global_size = collect($this->allMediaCollections)->sum('size');

        return view('spatie-medialibrary-manager::livewire.manager', compact('_global_count', '_global_size'));
    }
}