<?php

namespace BackupManager\Controller;
use App\Controller\Base;

class BackupManager extends Base {

    protected $layout = 'app:layouts/app.php';

    public function before() {
        if (!$this->helper('acl')->hasPermission('backupmanager/manage')) {
            $this->stop(401);
        }
    }

    public function index() {
        return $this->render('backupmanager:views/index.php', [
            'config' => $this->module('backupmanager')->config(),
            'backups' => $this->module('backupmanager')->getBackups()
        ]);
    }

    public function save() {
        $data = $this->app->request->body;

        $this->app->storage->setKey('backupmanager', $data);

        return ['success' => true];
    }

    public function create() {
        try {
            $this->module('backupmanager')->create();
            return ['success' => true, 'message' => 'Резервная копия успешно создана.'];
        } catch (\Exception $e) {
            return $this->stop(['error' => $e->getMessage()], 500);
        }
    }

    public function restore() {
        $filename = $this->param('file', null);

        if (!$filename) {
            return $this->stop(['error' => 'Имя файла не указано.'], 400);
        }

        try {
            $this->module('backupmanager')->restore($filename);
            return ['success' => true, 'message' => 'Система успешно восстановлена из резервной копии.'];
        } catch (\Exception $e) {
            return $this->stop(['error' => 'Ошибка восстановления: '.$e->getMessage()], 500);
        }
    }

    public function delete() {
        $filename = $this->param('file', null);
        $backupDir = $this->module('backupmanager')->getBackupDir();
        $file = $backupDir . '/' . basename($filename);

        if (!$filename || !file_exists($file)) {
            $this->stop(['error' => 'Файл не найден.'], 404);
        }

        unlink($file);
        return ['success' => true, 'message' => 'Резервная копия удалена.'];
    }

    public function download() {
        $filename = $this->param('file', null);
        $backupDir = $this->module('backupmanager')->getBackupDir();
        $file = $backupDir . '/' . basename($filename);

        if (!$filename || !file_exists($file)) {
            $this->stop(404);
        }

        $this->app->response->file($file);
    }
}
