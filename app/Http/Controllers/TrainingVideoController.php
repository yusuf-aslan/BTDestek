<?php

namespace App\Http\Controllers;

use App\Models\GeneralSetting;
use App\Models\TrainingVideo;
use App\Models\TrainingVideoCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TrainingVideoController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureEnabled($request);

        $user = $request->user();
        $canManage = TrainingVideo::canManage($user);
        $search = trim((string) $request->get('search'));

        // Modül listesi: yalnızca kullanıcının görebileceği video içeren aktif modüller (yöneticiler boşları da görür)
        $categories = TrainingVideoCategory::query()
            ->when(! $canManage, fn ($query) => $query->where('is_active', true))
            ->withCount(['videos' => fn ($query) => $query->visibleTo($user)])
            ->ordered()
            ->get()
            ->filter(fn (TrainingVideoCategory $category) => $canManage || $category->videos_count > 0)
            ->values();

        $activeCategory = filled($slug = $request->get('modul'))
            ? $categories->firstWhere('slug', $slug)
            : null;

        $videos = TrainingVideo::visibleTo($user)
            ->with('category')
            ->when($activeCategory, fn ($query) => $query->where('training_video_category_id', $activeCategory->id))
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhereHas('category', fn ($c) => $c->where('name', 'like', "%{$search}%"));
                });
            })
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        return view('training-videos.index', [
            'videos' => $videos,
            'categories' => $categories,
            'activeCategory' => $activeCategory,
            'totalCount' => TrainingVideo::visibleTo($user)->count(),
            'search' => $search,
            'canManage' => $canManage,
        ]);
    }

    public function show(Request $request, TrainingVideo $video)
    {
        $this->ensureEnabled($request);
        abort_unless($video->isVisibleTo($request->user()), 404);

        // Yönetici önizlemeleri izlenme sayısını artırmasın
        if (! TrainingVideo::canManage($request->user())) {
            $video->increment('views');
        }

        // Aynı modülün videoları sırasıyla; modülsüz videoda diğer videolar
        $related = TrainingVideo::visibleTo($request->user())
            ->with('category')
            ->whereKeyNot($video->getKey())
            ->when(
                $video->training_video_category_id,
                fn ($query) => $query->where('training_video_category_id', $video->training_video_category_id),
            )
            ->ordered()
            ->limit(8)
            ->get();

        return view('training-videos.show', [
            'video' => $video,
            'related' => $related,
            'canManage' => TrainingVideo::canManage($request->user()),
        ]);
    }

    public function video(Request $request, TrainingVideo $video): BinaryFileResponse
    {
        return $this->streamFile($request, $video, $video->isUpload() ? $video->video_path : null);
    }

    public function subtitle(Request $request, TrainingVideo $video): BinaryFileResponse
    {
        return $this->streamFile($request, $video, $video->subtitle_path, 'text/vtt; charset=UTF-8');
    }

    private function streamFile(Request $request, TrainingVideo $video, ?string $path, ?string $contentType = null): BinaryFileResponse
    {
        $this->ensureEnabled($request);
        abort_unless($video->isVisibleTo($request->user()), 404);

        $disk = Storage::disk(TrainingVideo::FILE_DISK);
        abort_if(blank($path) || ! $disk->exists($path), 404);

        // BinaryFileResponse Range isteklerini destekler; videoda ileri/geri sarma çalışır
        return response()->file($disk->path($path), [
            'Content-Type' => $contentType ?? ($disk->mimeType($path) ?: 'application/octet-stream'),
            'Cache-Control' => 'private, max-age=3600',
            'X-Content-Type-Options' => 'nosniff',
        ]);
    }

    private function ensureEnabled(Request $request): void
    {
        $enabled = (bool) GeneralSetting::query()->value('training_videos_enabled');

        // Modül kapalıyken yöneticiler yayına almadan önce önizleyebilir
        abort_unless($enabled || TrainingVideo::canManage($request->user()), 404);
    }
}
