<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Announcement extends Model
{
    protected $fillable = ['title', 'content', 'image_url', 'image_path', 'image_paths', 'created_by'];

    protected function casts(): array
    {
        return ['image_paths' => 'array'];
    }

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function readByUsers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'announcement_reads')
            ->withPivot('read_at');
    }

    public function reactions(): HasMany
    {
        return $this->hasMany(AnnouncementReaction::class);
    }

    public function comments(): HasMany
    {
        return $this->hasMany(AnnouncementComment::class);
    }

    public function getImageDisplayUrlAttribute(): ?string
    {
        return $this->image_display_urls[0] ?? null;
    }

    public function getImageDisplayUrlsAttribute(): array
    {
        $paths = $this->storedImagePaths();

        if ($paths !== []) {
            if (count($paths) === 1) {
                return [route('announcements.image', $this, false)];
            }

            return array_map(
                fn (int $index) => route('announcements.image', [$this, $index], false),
                array_keys($paths)
            );
        }

        return $this->image_url ? [$this->image_url] : [];
    }

    public function storedImagePaths(): array
    {
        $paths = $this->image_paths ?? [];

        if ($paths !== []) {
            return $paths;
        }

        return $this->image_path ? [$this->image_path] : [];
    }
}
