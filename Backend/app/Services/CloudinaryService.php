<?php

namespace App\Services;

use Cloudinary\Api\Exception\ApiError;
use Cloudinary\Cloudinary;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class CloudinaryService
{
    private ?Cloudinary $client = null;

    public function isConfigured(): bool
    {
        if (! empty(config('cloudinary.url'))) {
            return true;
        }

        return ! empty(config('cloudinary.cloud_name'))
            && ! empty(config('cloudinary.api_key'))
            && ! empty(config('cloudinary.api_secret'));
    }

    private function getClient(): Cloudinary
    {
        if ($this->client === null) {
            if (! $this->isConfigured()) {
                throw new RuntimeException(
                    'Cloudinary não configurado. Defina CLOUDINARY_URL ou CLOUDINARY_CLOUD_NAME, CLOUDINARY_API_KEY e CLOUDINARY_API_SECRET.'
                );
            }

            $config = config('cloudinary.url')
                ?: [
                    'cloud' => [
                        'cloud_name' => config('cloudinary.cloud_name'),
                        'api_key' => config('cloudinary.api_key'),
                        'api_secret' => config('cloudinary.api_secret'),
                    ],
                ];

            $this->client = new Cloudinary($config);
        }

        return $this->client;
    }

    public function upload(UploadedFile $file, string $subfolder): string
    {
        $folder = trim(config('cloudinary.folder').'/'.$subfolder, '/');

        $result = $this->getClient()->uploadApi()->upload(
            $file->getRealPath(),
            ['folder' => $folder]
        );

        return $result['secure_url'];
    }

    public function deleteByUrl(?string $url): void
    {
        if (! $url) {
            return;
        }

        if ($this->isCloudinaryUrl($url)) {
            if (! $this->isConfigured()) {
                return;
            }

            $publicId = $this->extractPublicId($url);
            if (! $publicId) {
                return;
            }

            try {
                $this->getClient()->adminApi()->deleteAssets($publicId);
            } catch (ApiError $e) {
                Log::warning('Falha ao excluir imagem do Cloudinary', [
                    'url' => $url,
                    'public_id' => $publicId,
                    'error' => $e->getMessage(),
                ]);
            }

            return;
        }

        $nomeArquivo = str_replace(asset('storage/'), '', $url);
        if ($nomeArquivo && Storage::disk('public')->exists($nomeArquivo)) {
            Storage::disk('public')->delete($nomeArquivo);
        }
    }

    public function isCloudinaryUrl(?string $url): bool
    {
        return is_string($url) && str_contains($url, 'res.cloudinary.com');
    }

    private function extractPublicId(string $url): ?string
    {
        $path = parse_url($url, PHP_URL_PATH);
        if (! is_string($path) || ! str_contains($path, '/upload/')) {
            return null;
        }

        $afterUpload = explode('/upload/', $path, 2)[1] ?? '';
        $afterUpload = preg_replace('#^v\d+/#', '', $afterUpload);

        if ($afterUpload === '' || $afterUpload === null) {
            return null;
        }

        return preg_replace('/\.[^.\/]+$/', '', $afterUpload);
    }
}
