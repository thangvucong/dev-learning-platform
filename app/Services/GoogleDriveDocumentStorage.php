<?php

namespace App\Services;

use App\Models\ClassSession;
use App\Models\CourseClass;
use Google\Client as GoogleClient;
use Google\Service\Drive;
use Google\Service\Drive\DriveFile as GoogleDriveFile;
use Illuminate\Http\UploadedFile;
use RuntimeException;

class GoogleDriveDocumentStorage
{
    protected ?Drive $drive = null;

    public function upload(UploadedFile $file, CourseClass $courseClass, ?ClassSession $session = null): array
    {
        $folderId = $this->resolveTargetFolder($courseClass, $session);
        $metadata = new GoogleDriveFile([
            'name' => $this->safeFileName($file->getClientOriginalName()),
            'parents' => [$folderId],
            'mimeType' => $file->getClientMimeType(),
        ]);

        $created = $this->drive()->files->create($metadata, [
            'data' => file_get_contents($file->getRealPath()),
            'mimeType' => $file->getClientMimeType(),
            'uploadType' => 'multipart',
            'fields' => 'id,name,mimeType,size',
            'supportsAllDrives' => true,
        ]);

        return [
            'drive_file_id' => $created->id,
            'drive_folder_id' => $folderId,
        ];
    }

    public function download(string $fileId): string
    {
        $response = $this->drive()->files->get($fileId, [
            'alt' => 'media',
            'supportsAllDrives' => true,
        ]);

        return (string) $response->getBody();
    }

    public function delete(string $fileId): void
    {
        $this->drive()->files->delete($fileId, [
            'supportsAllDrives' => true,
        ]);
    }

    protected function resolveTargetFolder(CourseClass $courseClass, ?ClassSession $session = null): string
    {
        $rootFolderId = $this->normalizeDriveId((string) config('services.google_drive.document_root_folder_id'));

        if ($rootFolderId === '') {
            throw new RuntimeException('GOOGLE_DRIVE_DOCUMENT_ROOT_FOLDER_ID chưa được cấu hình.');
        }

        $classFolder = $this->ensureFolder(
            $this->folderName('class', $courseClass->id, $courseClass->code ?: $courseClass->name),
            $rootFolderId
        );

        if (!$session) {
            return $classFolder;
        }

        return $this->ensureFolder(
            $this->folderName('session', $session->id, $session->title ?: 'Buổi ' . $session->session_no),
            $classFolder
        );
    }

    protected function ensureFolder(string $name, string $parentId): string
    {
        $query = sprintf(
            "name = '%s' and mimeType = 'application/vnd.google-apps.folder' and '%s' in parents and trashed = false",
            $this->escapeQueryValue($name),
            $this->escapeQueryValue($parentId)
        );

        $options = $this->listOptions([
            'q' => $query,
            'fields' => 'files(id,name)',
            'pageSize' => 1,
        ]);

        $existing = $this->drive()->files->listFiles($options);
        if (count($existing->files) > 0) {
            return $existing->files[0]->id;
        }

        $folder = new GoogleDriveFile([
            'name' => $name,
            'parents' => [$parentId],
            'mimeType' => 'application/vnd.google-apps.folder',
        ]);

        $created = $this->drive()->files->create($folder, $this->writeOptions([
            'fields' => 'id',
        ]));

        return $created->id;
    }

    protected function drive(): Drive
    {
        if ($this->drive) {
            return $this->drive;
        }

        $refreshToken = (string) config('services.google_drive.refresh_token');
        if ($refreshToken !== '') {
            $this->drive = new Drive($this->oauthClient($refreshToken));

            return $this->drive;
        }

        $serviceAccountPath = (string) config('services.google_drive.service_account_path');
        if ($serviceAccountPath === '' || !is_file($serviceAccountPath)) {
            throw new RuntimeException('GOOGLE_DRIVE_REFRESH_TOKEN chưa được cấu hình hoặc GOOGLE_DRIVE_SERVICE_ACCOUNT_PATH chưa hợp lệ.');
        }

        $client = new GoogleClient();
        $client->setAuthConfig($serviceAccountPath);
        $client->setScopes([Drive::DRIVE]);

        $this->drive = new Drive($client);

        return $this->drive;
    }

    protected function oauthClient(string $refreshToken): GoogleClient
    {
        $clientId = (string) config('services.google.client_id');
        $clientSecret = (string) config('services.google.client_secret');

        if ($clientId === '' || $clientSecret === '') {
            throw new RuntimeException('GOOGLE_CLIENT_ID hoặc GOOGLE_CLIENT_SECRET chưa được cấu hình.');
        }

        $client = new GoogleClient();
        $client->setClientId($clientId);
        $client->setClientSecret($clientSecret);
        $client->setScopes([Drive::DRIVE]);

        $token = $client->fetchAccessTokenWithRefreshToken($refreshToken);
        if (isset($token['error'])) {
            throw new RuntimeException('GOOGLE_DRIVE_REFRESH_TOKEN không hợp lệ: ' . (string) ($token['error_description'] ?? $token['error']));
        }

        return $client;
    }

    protected function writeOptions(array $options = []): array
    {
        $options['supportsAllDrives'] = true;

        return $options;
    }

    protected function listOptions(array $options = []): array
    {
        $options = $this->writeOptions($options);
        $options['includeItemsFromAllDrives'] = true;

        $sharedDriveId = $this->normalizeDriveId((string) config('services.google_drive.shared_drive_id'));
        if ($sharedDriveId !== '') {
            $options['corpora'] = 'drive';
            $options['driveId'] = $sharedDriveId;
        }

        return $options;
    }

    protected function folderName(string $prefix, int $id, string $name): string
    {
        $cleanName = trim(preg_replace('/\s+/', ' ', $name));
        $cleanName = preg_replace('/[^\pL\pN\s._-]+/u', '', $cleanName);

        return $prefix . '-' . $id . '-' . ($cleanName ?: 'untitled');
    }

    protected function safeFileName(string $name): string
    {
        $cleanName = trim(preg_replace('/\s+/', ' ', $name));

        return $cleanName !== '' ? $cleanName : 'learning-material';
    }

    protected function escapeQueryValue(string $value): string
    {
        return str_replace("'", "\\'", $value);
    }

    protected function normalizeDriveId(string $value): string
    {
        $value = trim($value);
        if ($value === '') {
            return '';
        }

        if (preg_match('#/folders/([^/?]+)#', $value, $matches)) {
            return $matches[1];
        }

        return explode('?', $value, 2)[0];
    }
}
