<?php
namespace Ynotz\MediaManager\Services;

use Illuminate\Support\Collection;
use Ynotz\MediaManager\Contracts\GalleryInterface;
use Ynotz\MediaManager\Models\MediaItem;

class GalleryService implements GalleryInterface
{
    public function getMediaItems(): Collection
    {
        return MediaItem::all();
    }
}
?>
