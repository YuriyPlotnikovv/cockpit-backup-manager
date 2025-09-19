<?php

namespace BackupManager\Controller;

use App\Controller\Base;

class BackupManager extends Base
{

    protected $layout = 'app:layouts/app.php';

    public function before()
    {
        if (!$this->helper('acl')->hasPermission('backupmanager/manage')) {
            $this->stop(401);
        }
    }

    public function index()
    {
        return $this->render('backupmanager:views/index.php', [
            'backups' => $this->module('backupmanager')->getBackups(),
            'settings' => $this->module('backupmanager')->getSettings(),
        ]);
    }

    public function settings()
    {
        return $this->render('backupmanager:views/settings.php', [
            'settings' => $this->module('backupmanager')->getSettings(),
        ]);
    }

    public function save()
    {
        $data = $this->app->request->body;

        $this->app->dataStorage->setKey('backupmanager', 'settings', $data);

        return ['success' => true];
    }

    public function create()
    {
        try {
            $this->module('backupmanager')->create();
            return ['success' => true, 'message' => 'Резервная копия успешно создана.'];
        } catch (\Exception $e) {
            return $this->stop(['error' => $e->getMessage()], 500);
        }
    }

    public function restore()
    {
        $filename = $this->param('file', null);

        if (!$filename) {
            return $this->stop(['error' => 'Имя файла не указано.'], 400);
        }

        try {
            $this->module('backupmanager')->restore($filename);
            return ['success' => true, 'message' => 'Система успешно восстановлена из резервной копии.'];
        } catch (\Exception $e) {
            return $this->stop(['error' => 'Ошибка восстановления: ' . $e->getMessage()], 500);
        }
    }

    public function delete()
    {
        $filename = $this->param('file', null);
        $backupDir = $this->module('backupmanager')->getBackupDir();
        $file = $backupDir . '/' . basename($filename);

        if (!$filename || !file_exists($file)) {
            $this->stop(['error' => 'Файл не найден.'], 404);
        }

        unlink($file);
        return ['success' => true, 'message' => 'Резервная копия удалена.'];
    }

    public function download()
    {
        $filename = $this->param('file', null);
        $backupDir = $this->module('backupmanager')->getBackupDir(false);
        $file = $backupDir . '/' . basename($filename);

        if (!$filename || !file_exists($file)) {
            return $this->stop(404);
        }

        header('Content-Description: File Transfer');
        header('Content-Type: application/zip');
        header('Content-Disposition: attachment; filename="' . basename($file) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));

        ob_clean();
        flush();
        readfile($file);

        exit;
    }
}
