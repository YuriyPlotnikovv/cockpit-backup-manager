<?php

namespace Backup\Controller;

use App\Controller\Base;

class Backup extends Base
{
    protected $layout = 'app:layouts/app.php';

    public function before()
    {
        if (!$this->helper('acl')->hasPermission('backup/manage')) {
            $this->stop(401);
        }
    }

    public function index()
    {
        return $this->render('backup:views/index.php', [
            'backups' => $this->module('backup')->getBackups(),
            'settings' => $this->module('backup')->getSettings(),
        ]);
    }

    public function settings()
    {
        return $this->render('backup:views/settings.php', [
            'settings' => $this->module('backup')->getSettings(),
        ]);
    }

    public function save()
    {
        $data = $this->app->request->body;

        if (!isset($data['inclusions'], $data['exclusions'])) {
            return $this->stop(['error' => t('Invalid settings data.')], 400);
        }

        if (empty($data['inclusions'])) {
            return $this->stop(['error' => t('Please select at least one section to include in the backup')], 400);
        }

        $validInclusions = ['Core', 'Site', 'Database'];

        foreach ($data['inclusions'] as $inclusion) {
            if (!in_array($inclusion, $validInclusions, true)) {
                return $this->stop(['error' => t('Invalid inclusion specified.')], 400);
            }
        }

        $this->dataStorage->setKey('backup', 'settings', $data);

        return ['success' => true, 'message' => t('Settings saved successfully!')];
    }

    public function getBackupsList()
    {
        try {
            $backups = $this->module('backup')->getBackups();

            return ['success' => true, 'backups' => $backups];
        } catch (\Exception $e) {
            return $this->stop(['error' => $e->getMessage()], 500);
        }
    }

    public function create()
    {
        try {
            $this->module('backup')->create();

            return $this->jsonResponse(true, t('The backup has been created successfully!'));
        } catch (\Exception $e) {
            return $this->jsonResponse(false, $e->getMessage(), 500);
        }
    }

    protected function jsonResponse(bool $success, string $message, int $statusCode = 200): array
    {
        $response = ['success' => $success];

        if ($success) {
            $response['message'] = $message;
        } else {
            $response['error'] = $message;
        }

        $this->app->response->status = $statusCode;

        return $response;
    }

    public function restore()
    {
        $filename = $this->param('file');

        if (!$filename) {
            return $this->jsonResponse(false, t('The backup file name is not specified.'), 400);
        }

        if (!$this->isValidBackupFilename($filename)) {
            return $this->jsonResponse(false, t('Invalid filename.'), 400);
        }

        try {
            $this->module('backup')->restore($filename);

            return $this->jsonResponse(true, t('The backup was successfully restored.'));
        } catch (\Exception $e) {
            return $this->jsonResponse(false, t('Backup recovery error: ') . $e->getMessage(), 500);
        }
    }

    protected function isValidBackupFilename(string $filename): bool
    {
        if (!preg_match('/^[a-zA-Z0-9._-]+\.tar\.gz$/', $filename)) {
            return false;
        }

        if (str_contains($filename, '..') || str_contains($filename, '/')) {
            return false;
        }

        return true;
    }

    public function delete()
    {
        $filename = $this->param('file');

        if (!$filename) {
            return $this->jsonResponse(false, t('The backup file name is not specified.'), 400);
        }

        if (!$this->isValidBackupFilename($filename)) {
            return $this->jsonResponse(false, t('Invalid filename.'), 400);
        }

        try {
            $this->module('backup')->deleteBackup($filename);

            return $this->jsonResponse(true, t('The backup has been deleted.'));
        } catch (\Exception $e) {
            return $this->jsonResponse(false, t('Delete error: ') . $e->getMessage(), 500);
        }
    }

    public function download()
    {
        $filename = $this->param('file');

        if (!$filename) {
            return $this->stop(404);
        }

        if (!$this->isValidBackupFilename($filename)) {
            return $this->stop(404);
        }

        $backupDir = $this->module('backup')->getBackupDir(false);
        $file = $backupDir . '/' . basename($filename);

        if (!file_exists($file)) {
            return $this->stop(404);
        }

        if (!$this->isFileInDirectory($file, $backupDir)) {
            return $this->stop(404);
        }

        $this->sendFile($file, 'application/gzip');

        return true;
    }

    public function downloadRestoreScript()
    {
        $scriptPath = $this->module('backup')->getRestoreScriptPath();
        $this->sendFile($scriptPath, 'application/x-php');

        return true;
    }

    protected function isFileInDirectory(string $filePath, string $directory): bool
    {
        $realFilePath = realpath($filePath);
        $realDirectory = realpath($directory);

        if ($realFilePath === false || $realDirectory === false) {
            return false;
        }

        $normalizedRealFilePath = str_replace('/', DIRECTORY_SEPARATOR, $realFilePath);
        $normalizedRealDirectory = str_replace('/', DIRECTORY_SEPARATOR, $realDirectory);

        return str_starts_with($normalizedRealFilePath, $normalizedRealDirectory);
    }

    protected function sendFile(string $filePath, string $mimeType): void
    {
        if (!file_exists($filePath) || !is_readable($filePath)) {
            $this->stop(404);
        }

        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        header('Content-Transfer-Encoding: binary');

        if (ob_get_level()) {
            ob_end_clean();
        }

        readfile($filePath);

        exit;
    }
}
