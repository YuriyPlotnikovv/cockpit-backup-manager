<?php

namespace Backup\Controller;

use App\Controller\Base;

class Backup extends Base
{
    protected $layout = 'app:layouts/app.php';

    public function before()
    {
        if (!$this->helper('acl')->isAllowed('backup/manage')) {
            $this->stop(401);
        }
    }

    public function index()
    {
        return $this->render('backup:views/index.php', [
            'backups' => $this->module('backup')->getBackups(),
            'settings' => $this->module('backup')->getSettings(),
            'moduleInfo' => $this->module('backup')->getInfo()
        ]);
    }

    public function saveSettings()
    {
        $data = $this->app->request->body;

        if (empty($data['inclusions'])) {
            return $this->jsonResponse(false, t('Please select at least one section to include in the backup'), 400);
        }

        $settingsToSave = [
            'inclusions' => $data['inclusions'],
            'exclusions' => $data['exclusions'],
            'mongoshPath' => $data['mongoshPath'] ?? '',
            'mongodumpPath' => $data['mongodumpPath'] ?? '',
            'mongorestorePath' => $data['mongorestorePath'] ?? '',
        ];

        $this->dataStorage->setKey('backup', 'settings', $settingsToSave);

        return $this->jsonResponse(true, t('Settings saved successfully!'));
    }

    public function getSettings()
    {
        try {
            $settings = $this->module('backup')->getSettings();

            return $this->jsonResponse(true, t('Settings loaded successfully'), 200, $settings);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, t('Failed to load settings.'), 500, ['details' => $e->getMessage()]);
        }
    }

    public function getBackups()
    {
        try {
            $backups = $this->module('backup')->getBackups();

            return $this->jsonResponse(true, t('Backup list loaded successfully'), 200, ['backups' => $backups]);
        } catch (\Exception $e) {
            return $this->jsonResponse(false, t('Failed to load backup list.'), 500, ['details' => $e->getMessage()]);
        }
    }

    public function createBackup()
    {
        try {
            $this->module('backup')->createBackup();

            return $this->jsonResponse(true, t('The backup has been created successfully!'));
        } catch (\Exception $e) {
            return $this->jsonResponse(false, t('Failed to create backup.'), 500, ['details' => $e->getMessage()]);
        }
    }

    public function restoreBackup()
    {
        $filename = $this->param('file');

        if (!$filename) {
            return $this->jsonResponse(false, t('The backup file name is not specified.'), 400);
        }

        if (!$this->isValidBackupFilename($filename)) {
            return $this->jsonResponse(false, t('Invalid filename.'), 400);
        }

        try {
            $this->module('backup')->restoreBackup($filename);

            return $this->jsonResponse(true, t('The backup was successfully restored.'));
        } catch (\Exception $e) {
            return $this->jsonResponse(false, t('Failed to restore backup.'), 500, ['details' => t('Recovery error: ') . $e->getMessage()]);
        }
    }

    public function deleteBackup()
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
            return $this->jsonResponse(false, t('Failed to delete backup. ') . $e->getMessage(), 500);
        }
    }

    public function downloadBackup()
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

    protected function jsonResponse(bool $success, string $message, int $statusCode = 200, array $data = []): array
    {
        $response = ['success' => $success];
        $response['message'] = $message;

        if (!$success && !empty($data['details'])) {
            $response['details'] = $data['details'];
        }

        if (!empty($data) && !isset($data['details'])) {
            $response = array_merge($response, $data);
        }

        $this->app->response->status = $statusCode;

        return $response;
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
}
