<?php

namespace Q2softwarenl\SpatieMedialibraryManager;

use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Livewire\Features\SupportFileUploads\TemporaryUploadedFile;
use Illuminate\Support\Str;

class ManagerFile
{
    public ?int $id;
    public ?string $collection_name;
    public ?string $name;
    public ?string $type;
    public ?string $file_name;
    public int $size = 0;
    public ?string $thumbnail_url;
    public ?string $theme_color;
    public string $date;
    public string $datetime;
    public string $filesize;
    public string $filesizemb;

    public function __construct(
        $id,
        $collection_name,
        $name,
        $file_name,
        $thumbnail_url = null,
        $size,
        $mime_type,
        $created_at = null
    ) {
        $this->id = $id;
        $this->collection_name = $collection_name;
        $this->name = $name;
        $this->file_name = $file_name;
        $this->date = ($created_at ?? now())->translatedFormat(__('blade_directives::format.date'));
        $this->datetime = ($created_at ?? now())->translatedFormat(__('blade_directives::format.datetime'));
        $this->size = $size;
        $this->type = collect([
            'excel' => Str::of($mime_type)->contains('officedocument.spreadsheet'),
            'image' => Str::of($mime_type)->contains('image'),
            'pdf' => strtolower($mime_type) === 'application/pdf',
            'word' => Str::of($mime_type)->contains('officedocument.word'),
            'zip' => strtolower($mime_type) === 'application/zip',
            ])->filter()->keys()->first() ?? 'unknown';
        $this->thumbnail_url = [
            'excel' => 'data:image/svg+xml;base64,'.base64_encode(file_get_contents(__DIR__.'/../resources/assets/file.svg')),
            'image' => $thumbnail_url,
            'pdf' => $thumbnail_url,
            'unknown' => 'data:image/svg+xml;base64,'.base64_encode(file_get_contents(__DIR__.'/../resources/assets/file.svg')),
            'word' => 'data:image/svg+xml;base64,'.base64_encode(file_get_contents(__DIR__.'/../resources/assets/file.svg')),
            'zip' => 'data:image/svg+xml;base64,'.base64_encode(file_get_contents(__DIR__.'/../resources/assets/file.svg')),
        ][$this->type];
        $this->theme_color = [
            'excel' => '#1d6f42',
            'image' => 'transparent',
            'pdf' => '#c61a0e',
            'unknown' => 'var(--fallback-bc, oklch(var(--bc) / 1))',
            'word' => '#103f91',
            'zip' => 'var(--fallback-bc, oklch(var(--bc) / 1))',
        ][$this->type];
        $this->filesize = number_format($size / 1024, 0, ',', '.') . ' kB';
        $this->filesizemb = (($size / 1048576) < 1 ? '<1' : number_format($size / 1048576, 0, ',', '.')) . ' MB';
    }

    public static function fromModel(Media $media)
    {
        return new self(
            id: $media->id,
            collection_name: $media->collection_name,
            name: $media->name,
            file_name: $media->file_name,
            thumbnail_url: $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : false,
            size: $media->size,
            mime_type: $media->mime_type,
            created_at: $media->created_at
        );
    }

    public static function fromJs(TemporaryUploadedFile $file): static
    {
        return new self(
            id: null,
            collection_name: null,
            name: pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            file_name: $file->getClientOriginalName(),
            thumbnail_url: null,
            size: $file->getSize(),
            mime_type: null
        );
    }

    public function toMediaCollection($collection_name): self
    {
        $this->collection_name = $collection_name;

        return $this;
    }

    public function toArray(): array 
    {
        return (array) $this;
    }

    public function __toArray(): array
    {
        return [
            'id' => $this->id,
            'collection_name' => $this->collection_name,
            'name' => $this->name,
            'file_name' => $this->file_name,
            'size' => $this->size,
            'thumbnail_url' => $this->thumbnail_url,
            'type' => $this->type,
            'theme_color' => $this->theme_color,
            'date' => $this->date,
            'datetime' => $this->datetime,
            'filesize' => $this->filesize,
            'filesizemb' => $this->filesizemb,
        ];
    }

}