<?php

namespace App\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Process;
use Throwable;

class VersionCheckService
{
    private const CACHE_KEY = 'system_info.version_check';

    private const CACHE_SECONDS = 600;

    public function check(bool $fresh = false): array
    {
        if ($fresh) {
            Cache::forget(self::CACHE_KEY);
        }

        return Cache::remember(self::CACHE_KEY, self::CACHE_SECONDS, fn () => $this->run());
    }

    public function local(): array
    {
        $log = $this->git(['log', '-1', '--format=%H%n%s%n%an%n%cI']);

        if ($log !== null) {
            [$sha, $message, $author, $date] = array_pad(explode("\n", $log, 4), 4, null);
            $dirty = $this->git(['status', '--porcelain', '--untracked-files=no']);

            return [
                'sha' => $sha,
                'message' => $message,
                'author' => $author,
                'date' => $date ? Carbon::parse($date) : null,
                'branch' => $this->git(['rev-parse', '--abbrev-ref', 'HEAD']),
                'modified_files' => $dirty === null || $dirty === '' ? 0 : count(explode("\n", $dirty)),
                'source' => 'git',
            ];
        }

        // git komutu yoksa veya çalışmıyorsa .git klasöründen commit bilgisini okumayı dene
        $sha = $this->readHeadFromFiles();

        return [
            'sha' => $sha,
            'message' => null,
            'author' => null,
            'date' => null,
            'branch' => null,
            'modified_files' => null,
            'source' => $sha ? 'files' : null,
        ];
    }

    private function run(): array
    {
        $local = $this->local();
        $repo = config('services.github.repo');
        $branch = config('services.github.branch');

        $result = [
            'checked_at' => now(),
            'repo' => $repo,
            'branch' => $branch,
            'local' => $local,
            'remote' => null,
            'status' => 'unknown',
            'behind_by' => 0,
            'ahead_by' => 0,
            'commits' => [],
            'error' => null,
        ];

        if (! $repo) {
            $result['error'] = 'GitHub reposu tanımlı değil (GITHUB_REPO).';

            return $result;
        }

        try {
            $latest = $this->github("repos/{$repo}/commits/{$branch}");

            if (! $latest->successful()) {
                $result['error'] = $this->githubError($latest->status());

                return $result;
            }

            $result['remote'] = [
                'sha' => $latest->json('sha'),
                'message' => strtok((string) $latest->json('commit.message'), "\n"),
                'author' => $latest->json('commit.author.name'),
                'date' => Carbon::parse($latest->json('commit.committer.date')),
                'url' => $latest->json('html_url'),
            ];

            if (! $local['sha']) {
                $result['error'] = 'Sunucudaki commit bilgisi okunamadı (.git klasörü bulunamadı).';

                return $result;
            }

            if ($local['sha'] === $result['remote']['sha']) {
                $result['status'] = 'identical';

                return $result;
            }

            $compare = $this->github("repos/{$repo}/compare/{$local['sha']}...{$branch}");

            if (! $compare->successful()) {
                // Yerel commit GitHub'da yoksa karşılaştırma 404 döner
                $result['status'] = $compare->status() === 404 ? 'unknown_local' : 'unknown';
                $result['error'] = $compare->status() === 404
                    ? 'Sunucudaki commit GitHub\'da bulunamadı. Push edilmemiş bir commit olabilir.'
                    : $this->githubError($compare->status());

                return $result;
            }

            // base = sunucu, head = GitHub dalı: "ahead" GitHub'ın önde, yani sunucunun geride olduğu anlamına gelir
            $result['status'] = match ($compare->json('status')) {
                'identical' => 'identical',
                'ahead' => 'behind',
                'behind' => 'ahead',
                default => 'diverged',
            };
            $result['behind_by'] = (int) $compare->json('ahead_by');
            $result['ahead_by'] = (int) $compare->json('behind_by');
            $result['commits'] = collect($compare->json('commits', []))
                ->reverse()
                ->take(20)
                ->map(fn ($commit) => [
                    'sha' => $commit['sha'],
                    'message' => strtok((string) $commit['commit']['message'], "\n"),
                    'author' => $commit['commit']['author']['name'] ?? null,
                    'date' => Carbon::parse($commit['commit']['committer']['date']),
                    'url' => $commit['html_url'],
                ])
                ->values()
                ->all();
        } catch (Throwable $e) {
            $result['error'] = 'GitHub\'a bağlanılamadı: '.$e->getMessage();
        }

        return $result;
    }

    private function github(string $path): \Illuminate\Http\Client\Response
    {
        $request = Http::acceptJson()
            ->withHeaders(['X-GitHub-Api-Version' => '2022-11-28'])
            ->withUserAgent(config('app.name').' version-check')
            ->connectTimeout(5)
            ->timeout(10);

        if ($token = config('services.github.token')) {
            $request = $request->withToken($token);
        }

        return $request->get('https://api.github.com/'.$path);
    }

    private function githubError(int $status): string
    {
        return match ($status) {
            401 => 'GitHub token geçersiz veya süresi dolmuş (GITHUB_TOKEN).',
            403 => 'GitHub erişimi reddedildi veya istek limiti aşıldı. GITHUB_TOKEN tanımlanması önerilir.',
            404 => config('services.github.token')
                ? 'Repo veya dal bulunamadı. GITHUB_REPO / GITHUB_BRANCH ve token yetkisini kontrol edin.'
                : 'Repo bulunamadı. Repo private ise .env dosyasına GITHUB_TOKEN eklenmelidir.',
            default => "GitHub beklenmeyen bir yanıt döndü (HTTP {$status}).",
        };
    }

    private function git(array $arguments): ?string
    {
        try {
            // Dosya sahibi farklı kullanıcıysa (www-data / root) git "dubious ownership" hatası vermesin
            $result = Process::path(base_path())
                ->timeout(5)
                ->run(array_merge(['git', '-c', 'safe.directory=*'], $arguments));

            return $result->successful() ? trim($result->output()) : null;
        } catch (Throwable) {
            return null;
        }
    }

    private function readHeadFromFiles(): ?string
    {
        $gitDir = base_path('.git');
        $head = @file_get_contents($gitDir.'/HEAD');

        if ($head === false) {
            return null;
        }

        $head = trim($head);

        if (! str_starts_with($head, 'ref: ')) {
            return $head;
        }

        $ref = substr($head, 5);
        $sha = @file_get_contents($gitDir.'/'.$ref);

        if ($sha !== false) {
            return trim($sha);
        }

        $packed = @file_get_contents($gitDir.'/packed-refs');
        if ($packed !== false && preg_match('/^([0-9a-f]{40}) '.preg_quote($ref, '/').'$/m', $packed, $m)) {
            return $m[1];
        }

        return null;
    }
}
