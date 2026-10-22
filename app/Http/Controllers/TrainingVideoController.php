<?php

namespace App\Http\Controllers;

use App\Models\GeneralSetting;
use App\Models\TrainingVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class TrainingVideoController extends Controller
{
    public function index(Request $request)
    {
        $this->ensureEnabled($request);

        $user = $request->user();
        $search = trim((string) $request->get('search'));
        $topic = $request->get('topic');

        $videos = TrainingVideo::visibleTo($user)
            ->when($search !== '', function ($query) use ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('title', 'like', "%{$search}%")
                        ->orWhere('description', 'like', "%{$search}%")
                        ->orWhere('topic', 'like', "%{$search}%");
                });
            })
            ->when(filled($topic), fn ($query) => $query->where('topic', $topic))
            ->ordered()
            ->paginate(12)
            ->withQueryString();

        $topics = TrainingVideo::visibleTo($user)
            ->whereNotNull('topic')
            ->distinct()
            ->orderBy('topic')
            ->pluck('topic');

        return view('training-videos.index', [
            'videos' => $videos,
            'topics' => $topics,
            'search' => $search,
            'activeTopic' => $topic,
            'canManage' => TrainingVideo::canManage($user),
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

        $related = TrainingVideo::visibleTo($request->user())
            ->whereKeyNot($video->getKey())
            ->when(filled($video->topic), fn ($query) => $query->orderByRaw('topic = ? desc', [$video->topic]))
            ->ordered()
            ->limit(6)
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
