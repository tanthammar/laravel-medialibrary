<?php

namespace Spatie\MediaLibrary\MediaCollections\Models\Collections;

use Illuminate\Contracts\Support\Htmlable;
use Illuminate\Database\Eloquent\Collection;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

/**
 * @template TKey of array-key
 * @template TModel of \Spatie\MediaLibrary\MediaCollections\Models\Media
 *
 * @extends Collection<TKey, TModel>
 */
class MediaCollection extends Collection implements Htmlable
{
    public ?string $collectionName = null;

    public ?string $formFieldName = null;

    /**
     * @return $this
     */
    public function collectionName(string $collectionName): self
    {
        $this->collectionName = $collectionName;

        return $this;
    }

    /**
     * @return $this
     */
    public function formFieldName(string $formFieldName): self
    {
        $this->formFieldName = $formFieldName;

        return $this;
    }

    public function totalSizeInBytes(): int
    {
        return $this->sum('size');
    }

    public function toHtml(): string
    {
        return e(json_encode(old($this->formFieldName ?? $this->collectionName) ?? $this->map(function (Media $media) {
            return [
                'name' => $media->name,
                'file_name' => $media->file_name,
                'uuid' => $media->uuid,
                'preview_url' => $media->preview_url,
                'original_url' => $media->original_url,
                'order' => $media->order_column,
                'custom_properties' => $media->custom_properties,
                'extension' => $media->extension,
                'size' => $media->size,
            ];
        })->keyBy('uuid')));
    }

    public function jsonSerialize(): array
    {
        if (config('media-library.use_default_collection_serialization')) {
            return parent::jsonSerialize();
        }

        if (! ($this->formFieldName ?? $this->collectionName)) {
            return [];
        }

        return old($this->formFieldName ?? $this->collectionName) ?? $this->map(function (Media $media) {
            return [
                'name' => $media->name,
                'file_name' => $media->file_name,
                'uuid' => $media->uuid,
                'preview_url' => $media->preview_url,
                'original_url' => $media->original_url,
                'order' => $media->order_column,
                'custom_properties' => $media->custom_properties,
                'extension' => $media->extension,
                'size' => $media->size,
            ];
        })->keyBy('uuid')->toArray();
    }
}
