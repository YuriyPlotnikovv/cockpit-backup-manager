<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
const SCRIPT_DIR = __DIR__;
set_time_limit(0);
ignore_user_abort(true);
session_start();
$GLOBALS['LOCALES_RU'] = [
    'Cockpit CMS Restore' => 'Восстановление Cockpit CMS',
    'Item not found, assuming deleted: %s' => 'Элемент не найден, считается удаленным: %s',
    'Deleting file: %s' => 'Удаление файла: %s',
    'Failed to delete file "%s". PHP Error: %s' => 'Не удалось удалить файл "%s". Ошибка PHP: %s',
    'File deleted: %s' => 'Файл удален: %s',
    'Attempting to delete directory: %s' => 'Попытка удаления директории: %s',
    'Failed to delete subdirectory "%s". PHP Error: %s' => 'Не удалось удалить поддиректорию "%s". Ошибка PHP: %s',
    'Failed to delete root directory "%s". PHP Error: %s' => 'Не удалось удалить корневую директорию "%s". Ошибка PHP: %s',
    'Directory deleted: %s' => 'Директория удалена: %s',
    'Error during recursive directory deletion of "%s": %s' => 'Ошибка при рекурсивном удалении директории "%s": %s',
    'Cannot delete item "%s": neither a file nor a directory.' => 'Невозможно удалить элемент "%s": ни файл, ни директория.',
    'Warning: Source path does not exist for copying: \'%s\'. Skipping.' => 'Предупреждение: Исходный путь для копирования не существует: \'%s\'. Пропускается.',
    'Error: Failed to create parent directory "%s" for file "%s".' => 'Ошибка: Не удалось создать родительскую директорию "%s" для файла "%s".',
    'Parent directory created: %s' => 'Родительская директория создана: %s',
    'Error: Failed to copy file from "%s" to "%s". PHP Error: %s' => 'Ошибка: Не удалось скопировать файл из "%s" в "%s". Ошибка PHP: %s',
    'File copied: %s to %s' => 'Файл скопирован: %s в %s',
    'Error: Failed to create directory "%s" for copying.' => 'Ошибка: Не удалось создать директорию "%s" для копирования.',
    'Directory created: %s' => 'Директория создана: %s',
    'Error: Failed to create subdirectory "%s" for copying.' => 'Ошибка: Не удалось создать поддиректорию "%s" для копирования.',
    'Subdirectory created: %s' => 'Поддиректория создана: %s',
    'Directory copied: %s to %s' => 'Директория скопирована: %s в %s',
    'Warning: Source \'%s\' is neither a file nor a directory. Skipping.' => 'Предупреждение: Источник \'%s\' не является ни файлом, ни директорией. Пропускается.',
    'Warning: Backup directory \'%s\' not found. Cannot scan for backups.' => 'Предупреждение: Директория резервных копий \'%s\' не найдена. Невозможно сканировать на наличие резервных копий.',
    'Bytes' => 'Байт',
    'KB' => 'КБ',
    'MB' => 'МБ',
    'GB' => 'ГБ',
    'TB' => 'ТБ',
    'Backup archive not found: \'%s\'.' => 'Архив резервной копии не найден: \'%s\'.',
    'Failed to create temporary directory for manifest extraction.' => 'Не удалось создать временную директорию для извлечения манифеста.',
    'Extracting manifest from \'%s\' to \'%s\'.' => 'Извлечение манифеста из \'%s\' в \'%s\'.',
    'Manifest file not found in the archive after extraction.' => 'Файл манифеста не найден в архиве после извлечения.',
    'Manifest extracted and parsed successfully.' => 'Манифест успешно извлечен и разобран.',
    'Error reading manifest from archive: %s' => 'Ошибка чтения манифеста из архива: %s',
    'Cleaning up temporary manifest directory: %s' => 'Очистка временной директории манифеста: %s',
    'Restore Confirmation' => 'Подтверждение восстановления',
    'WARNING!' => 'ВНИМАНИЕ!',
    'You are about to restore your site from backup' => 'Вы собираетесь восстановить ваш сайт из резервной копии',
    'This action <strong>WILL OVERWRITE ALL CURRENT FILES AND DATABASE</strong> on your server.' => 'Это действие <strong>ПЕРЕЗАПИШЕТ ВСЕ ТЕКУЩИЕ ФАЙЛЫ И БАЗУ ДАННЫХ</strong> на вашем сервере.',
    'It <strong>CANNOT BE UNDONE</strong>. Ensure you have backed up the current state of your site if necessary.' => 'Это <strong>НЕЛЬЗЯ ОТМЕНИТЬ</strong>. Убедитесь, что вы сделали резервную копию текущего состояния вашего сайта, если это необходимо.',
    'You must delete this script "restore.php" after completion!' => 'Вы должны удалить этот скрипт "restore.php" после завершения!',
    'Backup Details:' => 'Детали резервной копии:',
    'Filename:' => 'Имя файла:',
    'Cockpit Version:' => 'Версия Cockpit:',
    'Creation Date:' => 'Дата создания:',
    'DB Type:' => 'Тип БД:',
    'DB Name (MongoDB):' => 'Имя БД (MongoDB):',
    'DB DSN:' => 'DSN БД:',
    'may contain sensitive data' => 'может содержать конфиденциальные данные',
    'Path to Cockpit CMS installation (where index.php is):' => 'Путь к установке Cockpit CMS (где находится index.php):',
    'Path to Mongolite data folder (e.g., /cockpit/storage/data):' => 'Путь к папке данных Mongolite (например, /cockpit/storage/data):',
    'Start Restoration' => 'Начать восстановление',
    'Cancel' => 'Отмена',
    'Restoration Complete' => 'Восстановление завершено',
    'Restoration Log:' => 'Журнал восстановления:',
    'YOU MUST DELETE THIS SCRIPT "restore.php" FROM THE SERVER IMMEDIATELY AFTER COMPLETION.' => 'ВЫ ДОЛЖНЫ НЕМЕДЛЕННО УДАЛИТЬ ЭТОТ СКРИПТ "restore.php" С СЕРВЕРА ПОСЛЕ ЗАВЕРШЕНИЯ.',
    'Start Over' => 'Начать сначала',
    'Go to Cockpit' => 'Перейти в Cockpit',
    'PHP Phar extension is not enabled. It is required for backup/restore operations.' => 'Расширение PHP Phar не включено. Оно необходимо для операций резервного копирования/восстановления.',
    'shell_exec function is disabled. MongoDB restoration will not be possible.' => 'Функция shell_exec отключена. Восстановление MongoDB будет невозможно.',
    '\'mongorestore\' utility not found in PATH. MongoDB restoration will not be possible.' => 'Утилита \'mongorestore\' не найдена в PATH. Восстановление MongoDB будет невозможно.',
    '\'mongodump\' utility not found in PATH. MongoDB backup/restore might face issues.' => 'Утилита \'mongodump\' не найдена в PATH. Резервное копирование/восстановление MongoDB может столкнуться с проблемами.',
    "No backup files (.tar.gz) found in '%s'. Please place a backup file in this directory." => "Файлы резервных копий (.tar.gz) не найдены в '%s'. Пожалуйста, поместите файл резервной копии в эту директорию.",
    'Invalid backup filename specified.' => 'Указано неверное имя файла резервной копии.',
    'Internal Error: Chosen backup data could not be resolved, despite backups being found.' => 'Внутренняя ошибка: Выбранные данные резервной копии не удалось разрешить, несмотря на то, что резервные копии были найдены.',
    "Security Error: Resolved backup file '%s' is not within the expected backup directory '%s'." => "Ошибка безопасности: Разрешенный файл резервной копии '%s' не находится в ожидаемой директории резервных копий '%s'.",
    "Resolved backup file '%s' does not exist or is not a file. This indicates a filesystem issue or incorrect path resolving." => "Разрешенный файл резервной копии '%s' не существует или не является файлом. Это указывает на проблему файловой системы или неверное разрешение пути.",
    'Cockpit CMS installation path cannot be empty.' => 'Путь установки Cockpit CMS не может быть пустым.',
    "Invalid or unsafe Cockpit CMS installation path: '%s'. Path must be absolute and within the script's directory '%s'. Check for traversal attempts or non-existent parent directories." => "Неверный или небезопасный путь установки Cockpit CMS: '%s'. Путь должен быть абсолютным и находиться в директории скрипта '%s'. Проверьте попытки обхода или несуществующие родительские директории.",
    'Session data for backup manifest not found. Please restart the restore process from the beginning.' => 'Данные сессии для манифеста резервной копии не найдены. Пожалуйста, начните процесс восстановления с начала.', // Это сообщение теперь не должно появляться
    "Invalid or unsafe Mongolite data path: '%s'. Path must be absolute and within the Cockpit CMS installation path '%s'. Check for traversal attempts or non-existent parent directories." => "Неверный или небезопасный путь к данным Mongolite: '%s'. Путь должен быть абсолютным и находиться в пути установки Cockpit CMS '%s'. Проверьте попытки обхода или несуществующие родительские директории.",
    '--- Starting restoration process ---' => '--- Начало процесса восстановления ---',
    'Selected backup: %s' => 'Выбранная резервная копия: %s',
    'Backup source path: %s' => 'Исходный путь резервной копии: %s',
    'Cockpit CMS installation path: %s' => 'Путь установки Cockpit CMS: %s',
    'Mongolite data path: %s' => 'Путь к данным Mongolite: %s',
    'Extracting backup to temporary directory: %s' => 'Извлечение резервной копии во временную директорию: %s',
    'Failed to create temporary extraction directory "%s".' => 'Не удалось создать временную директорию для извлечения "%s".',
    'Backup extracted successfully.' => 'Резервная копия успешно извлечена.',
    'Error extracting backup: %s' => 'Ошибка извлечения резервной копии: %s',
    'Backup manifest loaded from backup file.' => 'Манифест резервной копии загружен из файла.',
    '--- Restoring Files ---' => '--- Восстановление файлов ---',
    'Removing existing Cockpit CMS installation at: %s' => 'Удаление существующей установки Cockpit CMS по адресу: %s',
    'Skipping deletion of restore.php itself: %s' => 'Пропускается удаление самого restore.php: %s',
    'Restoring Cockpit CMS files to: %s' => 'Восстановление файлов Cockpit CMS в: %s',
    'Warning: Cockpit CMS files directory not found in backup (expected: %s). Skipping Cockpit file restoration.' => 'Предупреждение: Директория файлов Cockpit CMS не найдена в резервной копии (ожидалось: %s). Пропускается восстановление файлов Cockpit.',
    'Restoring project root files to: %s' => 'Восстановление файлов корня проекта в: %s',
    'Removing existing item \'%s\' before restore.' => 'Удаление существующего элемента \'%s\' перед восстановлением.',
    'File restoration complete.' => 'Восстановление файлов завершено.',
    '--- Restoring Database ---' => '--- Восстановление базы данных ---',
    'Mongolite database restoration is implicitly handled by "Core" files restoration. No separate database dump restore needed.' => 'Восстановление базы данных Mongolite неявно обрабатывается при восстановлении файлов "Ядра". Отдельное восстановление дампа базы данных не требуется.',
    'Warning: Failed to ensure Mongolite data directory exists at \'%s\'.' => 'Предупреждение: Не удалось убедиться в существовании директории данных Mongolite по адресу \'%s\'.',
    'Error: shell_exec function is disabled. MongoDB restoration not possible.' => 'Ошибка: Функция shell_exec отключена. Восстановление MongoDB невозможно.',
    "Error: 'mongorestore' utility not found. MongoDB restoration not possible." => "Ошибка: Утилита 'mongorestore' не найдена. Восстановление MongoDB невозможно.",
    "MongoDB archive file not found in backup at '%s'. Skipping database restoration." => "Ошибка: Архивный файл MongoDB не найден в резервной копии по адресу '%s'. Пропускается восстановление базы данных.",
    'Database DSN or name not found in manifest. Cannot restore MongoDB.' => 'Ошибка: DSN или имя базы данных не найдены в манифесте. Невозможно восстановить MongoDB.',
    'Restoring MongoDB database.' => 'Восстановление базы данных MongoDB.',
    '  Database DSN: %s' => '  DSN базы данных: %s',
    '  Database Name: %s' => '  Имя базы данных: %s',
    '  Archive: %s' => '  Архив: %s',
    'Executing command: %s' => 'Выполнение команды: %s',
    'MongoDB restoration output:\n%s' => 'Вывод восстановления MongoDB:\n%s',
    'Error: MongoDB restoration failed. Check the output above and ensure mongorestore utility is installed and accessible.' => 'Ошибка: Восстановление MongoDB завершилось сбоем. Проверьте вывод выше и убедитесь, что утилита mongorestore установлена и доступна.',
    'MongoDB database restored.' => 'База данных MongoDB восстановлена.',
    'Warning: Unsupported database type \'%s\' found in manifest. Skipping database restoration.' => 'Предупреждение: Неподдерживаемый тип базы данных \'%s\' найден в манифесте. Пропускается восстановление базы данных.',
    '--- Cleanup ---' => '--- Очистка ---',
    'Removing temporary extraction directory: %s' => 'Удаление временной директории извлечения: %s',
    'Clearing Cockpit CMS cache at \'%s\'...' => 'Очистка кэша Cockpit CMS по адресу \'%s\'...',
    'Warning: Failed to recreate Cockpit CMS cache directory: \'%s\'.' => 'Предупреждение: Не удалось воссоздать директорию кэша Cockpit CMS: \'%s\'.',
    'Cockpit CMS cache cleared.' => 'Кэш Cockpit CMS очищен.',
    '--- Restoration Complete ---' => '--- Восстановление завершено ---',
    'CRITICAL ERROR: %s' => 'КРИТИЧЕСКАЯ ОШИБКА: %s',
    'Restoration failed: %s' => 'Восстановление завершилось сбоем: %s',
    'Site restored successfully!' => 'Сайт успешно восстановлен!',
    'An unexpected error occurred during page setup or no action was taken. This might indicate that no backup was found or a critical error prevented rendering.' => 'Произошла непредвиденная ошибка при настройке страницы или не было предпринято никаких действий. Это может указывать на то, что резервная копия не найдена или критическая ошибка предотвратила рендеринг.',
    'Unknown error' => 'Неизвестная ошибка',
    'Invalid Cockpit CMS installation path: \'%s\'. Path is not resolvable.' => 'Неверный путь установки Cockpit CMS: \'%s\'. Путь неразрешим.', // Это сообщение теперь не должно появляться при обычном ходе
    'Internal Error: Script directory path \'%s\' is not resolvable.' => 'Внутренняя ошибка: Путь к директории скрипта \'%s\' неразрешим.',
    'Invalid Mongolite data path: \'%s\'. Path is not resolvable.' => 'Неверный путь к данным Mongolite: \'%s\'. Путь неразрешим.', // Это сообщение теперь не должно появляться при обычном ходе
    'Cockpit CMS installation path \'%s\' does not exist yet. Will attempt to create it.' => 'Путь установки Cockpit CMS \'%s\' еще не существует. Будет предпринята попытка его создания.',
    'Mongolite data path \'%s\' does not exist yet. Will attempt to create it.' => 'Путь к данным Mongolite \'%s\' еще не существует. Будет предпринята попытка его создания.',
    'Invalid or unsafe Cockpit CMS installation path: \'%s\'. Path must be absolute and within the script\'s directory \'%s\'. Check for traversal attempts or non-existent parent directories.' => 'Неверный или небезопасный путь установки Cockpit CMS: \'%s\'. Путь должен быть абсолютным и находиться в директории скрипта \'%s\'. Проверьте попытки обхода или несуществующие родительские директории.',
    'Invalid or unsafe Mongolite data path: \'%s\'. Path must be absolute and within the Cockpit CMS installation path \'%s\'. Check for traversal attempts or non-existent parent directories.' => 'Неверный или небезопасный путь к данным Mongolite: \'%s\'. Путь должен быть абсолютным и находиться в пути установки Cockpit CMS \'%s\'. Проверьте попытки обхода или несуществующие родительские директории.',
    'or' => 'или',
];


$GLOBALS['CURRENT_LANG'] = 'en';

function _l_load_locale($lang)
{
    if ($lang === 'ru' && isset($GLOBALS['LOCALES_RU'])) {
        $GLOBALS['LOCALE'] = $GLOBALS['LOCALES_RU'];
        return true;
    }
    return false;
}

function _l($key, $args = [])
{
    $translated = $GLOBALS['LOCALE'][$key] ?? $key;
    if (!empty($args)) {
        return sprintf($translated, ...$args);
    }
    return $translated;
}

$acceptedLanguages = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en';
$preferredLanguages = explode(',', $acceptedLanguages);

foreach ($preferredLanguages as $lang) {
    $lang = trim(explode(';', $lang)[0]);
    $langShort = strtolower(substr($lang, 0, 2));

    if ($langShort === 'ru') {
        $GLOBALS['CURRENT_LANG'] = 'ru';
        _l_load_locale('ru');
        break;
    }
}

set_error_handler(function ($errno, $errstr, $errfile, $errline) {
    if (!(error_reporting() & $errno)) {
        return false;
    }
    if ($errno === E_WARNING || $errno === E_NOTICE || $errno === E_USER_WARNING || $errno === E_USER_NOTICE) {
        if (str_starts_with($errstr, 'mkdir():') || str_starts_with($errstr, 'rmdir():') || str_starts_with($errstr, 'unlink():') || str_starts_with($errstr, 'copy():')) {
            return false;
        }
    }
    throw new ErrorException($errstr, 0, $errno, $errfile, $errline);
});

$globalRestoreError = null;
$finalPageTitle = _l('Cockpit CMS Restore');
$outputBoxContentAccumulated = '';

function ui_message($message, $type = 'info')
{
    global $outputBoxContentAccumulated;
    $outputBoxContentAccumulated .= "<div class='message {$type}'>" . nl2br(htmlspecialchars($message)) . "</div>\n";
}

function rrmdir($path)
{
    $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    if (!file_exists($path)) {
        ui_message(_l('Item not found, assuming deleted: %s', [$path]), 'info');
        return true;
    }
    if (is_file($path)) {
        ui_message(_l('Deleting file: %s', [$path]), 'info');
        if (!@unlink($path)) {
            $error = error_get_last();
            throw new RuntimeException(sprintf(_l('Failed to delete file "%s". PHP Error: %s'), $path, $error['message'] ?? _l('Unknown error')));
        }
        ui_message(_l('File deleted: %s', [$path]), 'success');
        return true;
    }
    if (is_dir($path)) {
        ui_message(_l('Attempting to delete directory: %s', [$path]), 'info');
        try {
            $items = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($items as $item) {
                if ($item->isDir()) {
                    if (!@rmdir($item->getRealPath())) {
                        $error = error_get_last();
                        throw new RuntimeException(sprintf(_l('Failed to delete subdirectory "%s". PHP Error: %s'), $item->getRealPath(), $error['message'] ?? _l('Unknown error')));
                    }
                } else {
                    if (!@unlink($item->getRealPath())) {
                        $error = error_get_last();
                        throw new RuntimeException(sprintf(_l('Failed to delete file "%s". PHP Error: %s'), $item->getRealPath(), $error['message'] ?? _l('Unknown error')));
                    }
                }
            }
            if (!@rmdir($path)) {
                $error = error_get_last();
                throw new RuntimeException(sprintf(_l('Failed to delete root directory "%s". PHP Error: %s'), $path, $error['message'] ?? _l('Unknown error')));
            }
            ui_message(_l('Directory deleted: %s', [$path]), 'success');
            return true;
        } catch (Exception $e) {
            throw new RuntimeException(sprintf(_l('Error during recursive directory deletion of "%s": %s'), $path, $e->getMessage()));
        }
    }
    throw new RuntimeException(sprintf(_l('Cannot delete item "%s": neither a file nor a directory.'), $path));
}

function rcopy($source, $dest)
{
    $source = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $source);
    $dest = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $dest);
    if (!file_exists($source)) {
        ui_message(_l("Warning: Source path does not exist for copying: '%s'. Skipping.", [$source]), 'warning');
        return;
    }
    if (is_file($source)) {
        $destDir = dirname($dest);
        if (!is_dir($destDir)) {
            if (!mkdir($destDir, 0755, true) && !is_dir($destDir)) {
                throw new RuntimeException(sprintf(_l('Error: Failed to create parent directory "%s" for file "%s".'), $destDir, $dest));
            }
            ui_message(_l('Parent directory created: %s', [$destDir]), 'info');
        }
        if (!@copy($source, $dest)) {
            $error = error_get_last();
            throw new RuntimeException(sprintf(_l('Error: Failed to copy file from "%s" to "%s". PHP Error: %s'), $source, $dest, $error['message'] ?? _l('Unknown error')));
        }
        ui_message(_l('File copied: %s to %s', [$source, $dest]), 'info');
    } elseif (is_dir($source)) {
        if (!is_dir($dest)) {
            if (!mkdir($dest, 0755, true) && !is_dir($dest)) {
                throw new RuntimeException(sprintf(_l('Error: Failed to create directory "%s" for copying.'), $dest));
            }
            ui_message(_l('Directory created: %s', [$dest]), 'info');
        }
        $iterator = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($source, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );
        foreach ($iterator as $item) {
            $path = $dest . DIRECTORY_SEPARATOR . $iterator->getSubPathName();
            if ($item->isDir()) {
                if (!is_dir($path)) {
                    if (!mkdir($path, 0755, true) && !is_dir($path)) {
                        throw new RuntimeException(sprintf(_l('Error: Failed to create subdirectory "%s" for copying.'), $path));
                    }
                    ui_message(_l('Subdirectory created: %s', [$path]), 'info');
                }
            } else if (!@copy($item->getRealPath(), $path)) {
                $error = error_get_last();
                throw new RuntimeException(sprintf(_l('Error: Failed to copy file from "%s" to "%s". PHP Error: %s'), $item->getRealPath(), $path, $error['message'] ?? _l('Unknown error')));
            }
        }
        ui_message(_l('Directory copied: %s to %s', [$source, $dest]), 'info');
    } else {
        ui_message(_l("Warning: Source '%s' is neither a file nor a directory. Skipping.", [$source]), 'warning');
    }
}

function scan_for_backups($directory)
{
    $directory = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $directory);
    $backups = [];
    if (!is_dir($directory)) {
        ui_message(_l("Warning: Backup directory '%s' not found. Cannot scan for backups.", [$directory]), 'warning');
        return $backups;
    }
    $files = new DirectoryIterator($directory);
    foreach ($files as $file) {
        if ($file->isFile() && preg_match('/\.tar\.gz$/', $file->getFilename())) {
            $backups[] = [
                'path' => $file->getPathname(),
                'filename' => $file->getFilename(),
                'mtime' => $file->getMTime(),
                'size' => $file->getSize(),
            ];
        }
    }
    usort($backups, static fn($a, $b) => $b['mtime'] <=> $a['mtime']);
    return $backups;
}

function format_size($bytes)
{
    if ($bytes === 0) {
        return '0 Bytes';
    }
    $k = 1024;
    $sizes = [_l('Bytes'), _l('KB'), _l('MB'), _l('GB'), _l('TB')];
    $i = floor(log($bytes) / log($k));
    return round($bytes / pow($k, $i), 2) . ' ' . $sizes[$i];
}

function read_manifest_from_archive($archivePath)
{
    $archivePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $archivePath);
    if (!file_exists($archivePath)) {
        throw new RuntimeException(_l("Backup archive not found: '%s'.", [$archivePath]));
    }
    $tempManifestDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cockpit_manifest_extract_' . uniqid('', true);
    if (!mkdir($tempManifestDir, 0755, true) && !is_dir($tempManifestDir)) {
        throw new RuntimeException(_l('Failed to create temporary directory for manifest extraction.'));
    }
    ui_message(_l("Extracting manifest from '%s' to '%s'.", [$archivePath, $tempManifestDir]), 'info');
    try {
        $phar = new PharData($archivePath);
        $phar->extractTo($tempManifestDir, 'backup_manifest.json', true);
        $manifestFilePath = $tempManifestDir . DIRECTORY_SEPARATOR . 'backup_manifest.json';
        if (!file_exists($manifestFilePath)) {
            throw new Exception(_l('Manifest file not found in the archive after extraction.'));
        }
        $manifest = json_decode(file_get_contents($manifestFilePath), true, 512, JSON_THROW_ON_ERROR);
        ui_message(_l('Manifest extracted and parsed successfully.'), 'success');
        return $manifest;
    } catch (Exception $e) {
        throw new RuntimeException(_l('Error reading manifest from archive: %s', [$e->getMessage()]));
    } finally {
        if (is_dir($tempManifestDir)) {
            ui_message(_l('Cleaning up temporary manifest directory: %s', [$tempManifestDir]), 'info');
            @rrmdir($tempManifestDir);
        }
    }
}

function render_html_page($title, $bodyContent)
{
    ob_start(); ?>
    <!DOCTYPE html>
    <html lang="<?= htmlspecialchars($GLOBALS['CURRENT_LANG']) ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?= htmlspecialchars($title) ?> | Cockpit CMS Restore Tool</title>
        <style>
            body {
                font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Oxygen-Sans, Ubuntu, Cantarell, "Helvetica Neue", sans-serif;
                margin: 0;
                padding: 20px;
                background-color: #f0f2f5;
                color: #333;
                line-height: 1.6;
            }

            .container {
                max-width: 900px;
                margin: 20px auto;
                background-color: #ffffff;
                padding: 30px;
                border-radius: 10px;
                box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            }

            h1, h2 {
                color: #2c3e50;
                border-bottom: 1px solid #e0e0e0;
                padding-bottom: 15px;
                margin-bottom: 25px;
                font-size: 24px;
                font-weight: 600;
            }

            h2 {
                font-size: 20px;
                margin-top: 30px;
            }

            .warning {
                color: #8a6d3b;
                background-color: #fcf8e3;
                border: 1px solid #faebcc;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
                font-size: 14px;
                line-height: 1.5;
            }

            .error {
                color: #a94442;
                background-color: #f2dede;
                border: 1px solid #ebccd1;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
                font-size: 14px;
                line-height: 1.5;
            }

            .success {
                color: #3c763d;
                background-color: #dff0d8;
                border: 1px solid #d6e9c6;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
                font-size: 14px;
                line-height: 1.5;
            }

            .info {
                color: #31708f;
                background-color: #d9edf7;
                border: 1px solid #bce8f1;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
                font-size: 14px;
                line-height: 1.5;
            }

            .message strong {
                font-weight: 700;
            }

            .form-group {
                margin-bottom: 20px;
            }

            label {
                display: block;
                margin-bottom: 8px;
                font-weight: 600;
                color: #555;
            }

            input[type="text"], input[type="password"], select, input[type="file"] {
                width: 100%;
                padding: 12px;
                border: 1px solid #ccc;
                border-radius: 6px;
                box-sizing: border-box;
                font-size: 16px;
                transition: border-color 0.2s;
            }

            input[type="text"]:focus, input[type="password"]:focus, select:focus, input[type="file"]:focus {
                border-color: #007bff;
                outline: none;
                box-shadow: 0 0 0 2px rgba(0, 123, 255, 0.25);
            }

            button {
                background-color: #007bff;
                color: white;
                padding: 12px 25px;
                border: none;
                border-radius: 6px;
                cursor: pointer;
                font-size: 16px;
                font-weight: 600;
                transition: background-color 0.2s;
            }

            button:hover {
                background-color: #0056b3;
            }

            button:disabled {
                background-color: #cccccc;
                cursor: not-allowed;
            }

            .button-danger {
                background-color: #dc3545;
            }

            .button-danger:hover {
                background-color: #c82333;
            }

            .output-box {
                background-color: #f8f8f8;
                border: 1px solid #e7e7e7;
                padding: 15px;
                margin-bottom: 20px;
                border-radius: 8px;
                max-height: 350px;
                overflow-y: auto;
                overflow-x: hidden;
                white-space: pre-wrap;
                font-family: 'Roboto Mono', monospace;
                font-size: 13px;
                margin-top: 25px;
                line-height: 1.4;
                color: #444;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 20px;
            }

            th, td {
                border: 1px solid #ddd;
                padding: 10px;
                text-align: left;
            }

            th {
                background-color: #f2f2f2;
                font-weight: 600;
            }

            tr:nth-child(even) {
                background-color: #f9f9f9;
            }

            tr:hover {
                background-color: #f0f0f0;
            }

            .text-center {
                text-align: center;
            }

            .actions a {
                margin-right: 10px;
                color: #007bff;
                text-decoration: none;
            }

            .actions a:hover {
                text-decoration: underline;
            }

            .back-link {
                display: inline-block;
                margin-top: 20px;
                color: #007bff;
                text-decoration: none;
            }

            .back-link:hover {
                text-decoration: underline;
            }
        </style>
    </head>
    <body>
    <div class="container">
        <?= $bodyContent ?>
    </div>
    </body>
    </html>
    <?php return ob_get_clean();
}

function render_confirm_restore($backupFilename, $manifest, $warnings = [])
{
    ob_start();
    $displayDsn = $manifest['database']['dsn'] ?? 'N/A';
    if (str_starts_with($displayDsn, 'mongodb://') && str_contains($displayDsn, '@')) {
        $displayDsn = preg_replace('/(?<=:\/\/.*:).*?(?=@)/', '***', $displayDsn);
    }
    $defaultCockpitPath = SCRIPT_DIR . DIRECTORY_SEPARATOR . 'cockpit';
    $defaultMongoliteDataPath = $defaultCockpitPath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data';
    ?>
    <h1><?= _l('Restore Confirmation') ?></h1>
    <div class="warning">
        <strong><?= _l('WARNING!') ?></strong> <?= _l('You are about to restore your site from backup') ?>
        "<strong><?= htmlspecialchars($backupFilename) ?></strong>".
        <br><?= _l('This action <strong>WILL OVERWRITE ALL CURRENT FILES AND DATABASE</strong> on your server.') ?>
        <br><?= _l('It <strong>CANNOT BE UNDONE</strong>. Ensure you have backed up the current state of your site if necessary.') ?>
        <br><br><?= _l('You must delete this script "restore.php" after completion!') ?>
    </div>
    <?php if (!empty($warnings)): foreach ($warnings as $warn): ?>
    <div class="message warning"><?= htmlspecialchars($warn) ?></div>
<?php endforeach; endif; ?>
    <h3><?= _l('Backup Details:') ?></h3>
    <ul>
        <li><strong><?= _l('Filename:') ?></strong> <?= htmlspecialchars($backupFilename) ?></li>
        <li>
            <strong><?= _l('Cockpit Version:') ?></strong> <?= htmlspecialchars($manifest['cockpit_version'] ?? 'N/A') ?>
        </li>
        <li><strong><?= _l('Creation Date:') ?></strong> <?= date('Y-m-d H:i:s', $manifest['timestamp'] ?? time()) ?>
        </li>
        <li><strong><?= _l('DB Type:') ?></strong> <?= htmlspecialchars($manifest['database']['type'] ?? 'N/A') ?></li>
        <li>
            <strong><?= _l('DB Name (MongoDB):') ?></strong> <?= htmlspecialchars($manifest['database']['db_name'] ?? 'N/A') ?>
        </li>
        <li><strong><?= _l('DB DSN:') ?></strong> <code><?= htmlspecialchars($displayDsn) ?></code> <span
                style="font-size:0.8em;color:#888;">(<?= _l('may contain sensitive data') ?>)</span></li>
    </ul>
    <form method="POST" action="?state=perform_restore&backup=<?= urlencode($backupFilename) ?>">
        <div class="form-group">
            <label for="cockpit_path"><?= _l('Path to Cockpit CMS installation (where index.php is):') ?></label>
            <input type="text" id="cockpit_path" name="cockpit_path"
                   value="<?= htmlspecialchars($defaultCockpitPath) ?>" required>
        </div>
        <?php
        if (($manifest['database']['type'] ?? null) === 'mongolite'): ?>
            <div class="form-group">
                <label
                    for="mongolite_data_path"><?= _l('Path to Mongolite data folder (e.g., /cockpit/storage/data):') ?></label>
                <input type="text" id="mongolite_data_path" name="mongolite_data_path"
                       value="<?= htmlspecialchars($defaultMongoliteDataPath) ?>"
                       required>
            </div>
        <?php endif; ?>
        <button type="submit" class="button-danger" name="action"
                value="restore_confirm"><?= _l('Start Restoration') ?></button>
        <a href="?" class="back-link"><?= _l('Cancel') ?></a>
    </form>
    <?php return ob_get_clean();
}

function render_final_status($message, $type = 'success', $outputContent = '')
{
    ob_start(); ?>
    <h1><?= _l('Restoration Complete') ?></h1>
    <div class="message <?= htmlspecialchars($type) ?>"><?= nl2br(htmlspecialchars($message)) ?></div>
    <?php if ($outputContent): ?>
    <h2><?= _l('Restoration Log:') ?></h2>
    <div class="output-box" id="restore-output">
        <?= $outputContent ?>
    </div>
<?php endif; ?>
    <div class="warning">
        <strong><?= _l('YOU MUST DELETE THIS SCRIPT "restore.php" FROM THE SERVER IMMEDIATELY AFTER COMPLETION.') ?></strong>
    </div>
    <a href="?" class="back-link"><?= _l('Start Over') ?></a> <?= _l('or') ?> <a
    href="/cockpit"
    class="back-link"><?= _l('Go to Cockpit') ?></a>
    <?php return ob_get_clean();
}

function normalize_path_for_comparison(string $path): string
{
    $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    return rtrim($path, DIRECTORY_SEPARATOR);
}

function is_target_path_logically_within_base(string $targetPath, string $basePath): bool
{
    $normalizedTargetPath = normalize_path_for_comparison($targetPath);
    $normalizedBasePath = normalize_path_for_comparison($basePath);

    if (str_contains($normalizedTargetPath, DIRECTORY_SEPARATOR . '..') || str_contains($normalizedTargetPath, '..' . DIRECTORY_SEPARATOR)) {
        return false;
    }
    if ($normalizedTargetPath === '..' || $normalizedTargetPath === '.') {
        return false;
    }

    if ($normalizedTargetPath === $normalizedBasePath) {
        return true;
    }
    if (str_starts_with($normalizedTargetPath, $normalizedBasePath . DIRECTORY_SEPARATOR)) {
        return true;
    }

    return false;
}

function chmod_recursive_dir(string $path, int $dirPermissions = 0755, int $filePermissions = 0644): void
{
    $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    if (!file_exists($path)) {
        ui_message(sprintf(_l('Warning: Path not found for permission setting: %s. Skipping.'), $path), 'warning');
        return;
    }

    ui_message(sprintf(_l('Setting appropriate file permissions for %s...'), $path), 'info');

    try {
        if (is_file($path)) {
            if (!@chmod($path, $filePermissions)) {
                $error = error_get_last();
                ui_message(sprintf(_l('Failed to set permissions for %s. PHP Error: %s'), $path, $error['message'] ?? _l('Unknown error')), 'error');
            }
            return;
        }

        $items = new RecursiveIteratorIterator(
            new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
            RecursiveIteratorIterator::SELF_FIRST
        );

        foreach ($items as $item) {
            $currentPath = $item->getRealPath();
            if ($currentPath === false) {
                ui_message(sprintf(_l('Warning: Skipping inaccessible item for permission setting: %s.'), $item->getPathname()), 'warning');
                continue;
            }
            if ($item->isDir()) {
                if (!@chmod($currentPath, $dirPermissions)) {
                    $error = error_get_last();
                    ui_message(sprintf(_l('Failed to set permissions for directory %s. PHP Error: %s'), $currentPath, $error['message'] ?? _l('Unknown error')), 'error');
                }
            } elseif ($item->isFile()) {
                if (!@chmod($currentPath, $filePermissions)) {
                    $error = error_get_last();
                    ui_message(sprintf(_l('Failed to set permissions for file %s. PHP Error: %s'), $currentPath, $error['message'] ?? _l('Unknown error')), 'error');
                }
            }
        }
        if (!@chmod($path, $dirPermissions)) {
            $error = error_get_last();
            ui_message(sprintf(_l('Failed to set permissions for root directory %s. PHP Error: %s'), $path, $error['message'] ?? _l('Unknown error')), 'error');
        } else {
            ui_message(sprintf(_l('Permissions set for %s.'), $path), 'success');
        }

    } catch (Exception $e) {
        ui_message(sprintf(_l('Error setting permissions for %s: %s'), $path, $e->getMessage()), 'error');
    }
}


$renderTitle = _l('Cockpit CMS Restore');
$pageContent = '';
$globalRestoreError = null;
$warningsToRender = [];
$restoreSuccess = false;
$tempExtractBaseDir = null;
try {
    if (!class_exists('PharData')) {
        throw new RuntimeException(_l('PHP Phar extension is not enabled. It is required for backup/restore operations.'));
    }
    $mongo_restore_check = function_exists('shell_exec') ? shell_exec('command -v mongorestore 2>/dev/null') : null;
    $mongo_dump_check = function_exists('shell_exec') ? shell_exec('command -v mongodump 2>/dev/null') : null;
    if (!function_exists('shell_exec')) {
        $warningsToRender[] = _l('shell_exec function is disabled. MongoDB restoration will not be possible.');
    } elseif (empty($mongo_restore_check)) {
        $warningsToRender[] = _l("'mongorestore' utility not found in PATH. MongoDB restoration will not be possible.");
    } elseif (empty($mongo_dump_check)) {
        $warningsToRender[] = _l("'mongodump' utility not found in PATH. MongoDB backup/restore might face issues.");
    }
    $state = $_GET['state'] ?? 'confirm_restore';
    $selectedBackupFilename = (isset($_GET['backup']) && $_GET['backup'] !== '') ? $_GET['backup'] : null;
    $action = $_POST['action'] ?? '';
    $backupDir = SCRIPT_DIR;
    $availableBackups = scan_for_backups($backupDir);
    if (empty($availableBackups)) {
        throw new RuntimeException(sprintf(_l("No backup files (.tar.gz) found in '%s'. Please place a backup file in this directory."), htmlspecialchars($backupDir)));
    }
    $chosenBackupData = null;
    if ($selectedBackupFilename !== null) {
        if (!preg_match('/^[a-zA-Z0-9._-]+\.tar\.gz$/', $selectedBackupFilename) || str_contains($selectedBackupFilename, '..') || str_contains($selectedBackupFilename, '/')) {
            throw new RuntimeException(_l('Invalid backup filename specified.'));
        }
        foreach ($availableBackups as $backup) {
            if ($backup['filename'] === $selectedBackupFilename) {
                $chosenBackupData = $backup;
                break;
            }
        }
    }
    if ($chosenBackupData === null) {
        $chosenBackupData = $availableBackups[0];
        $selectedBackupFilename = $chosenBackupData['filename'];
        if ($state === 'confirm_restore' && !isset($_GET['autoselected']) && !isset($_POST['action'])) {
            header('Location: ?state=confirm_restore&backup=' . urlencode($selectedBackupFilename) . '&autoselected=1');
            exit;
        }
    }
    if ($chosenBackupData === null) {
        throw new RuntimeException(_l('Internal Error: Chosen backup data could not be resolved, despite backups being found.'));
    }
    $backupFilePath = $chosenBackupData['path'];
    $realBackupFilePath = realpath($backupFilePath);
    $realBackupDir = realpath($backupDir);
    if ($realBackupFilePath === false || $realBackupDir === false || !str_starts_with($realBackupFilePath, $realBackupDir)) {
        throw new RuntimeException(sprintf(_l("Security Error: Resolved backup file '%s' is not within the expected backup directory '%s'."), $backupFilePath, $backupDir));
    }
    if (!file_exists($backupFilePath) || !is_file($backupFilePath)) {
        throw new RuntimeException(sprintf(_l("Resolved backup file '%s' does not exist or is not a file. This indicates a filesystem issue or incorrect path resolving."), $backupFilePath));
    }
    if ($state === 'perform_restore' && $action === 'restore_confirm') {
        $inputCockpitRootPath = $_POST['cockpit_path'] ?? '';
        $inputCockpitRootPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $inputCockpitRootPath);
        $inputCockpitRootPath = rtrim($inputCockpitRootPath, DIRECTORY_SEPARATOR);
        if (empty($inputCockpitRootPath)) {
            throw new RuntimeException(_l('Cockpit CMS installation path cannot be empty.'));
        }

        $realScriptDir = realpath(SCRIPT_DIR);
        if ($realScriptDir === false) {
            throw new RuntimeException(sprintf(_l("Internal Error: Script directory path '%s' is not resolvable."), SCRIPT_DIR));
        }
        $normalizedRealScriptDir = normalize_path_for_comparison($realScriptDir);

        if (!is_target_path_logically_within_base($inputCockpitRootPath, $normalizedRealScriptDir)) {
            throw new RuntimeException(sprintf(_l("Invalid or unsafe Cockpit CMS installation path: '%s'. Path must be absolute and within the script's directory '%s'. Check for traversal attempts or non-existent parent directories."), $inputCockpitRootPath, $normalizedRealScriptDir));
        }

        $realInputCockpitRootPath = realpath($inputCockpitRootPath);
        if ($realInputCockpitRootPath === false) {
            ui_message(sprintf(_l("Cockpit CMS installation path '%s' does not exist yet. Will attempt to create it."), $inputCockpitRootPath), 'info');
        } else {
            $inputCockpitRootPath = normalize_path_for_comparison($realInputCockpitRootPath);
        }

        $manifest = read_manifest_from_archive($backupFilePath);

        $dbType = $manifest['database']['type'] ?? null;
        $inputMongoliteDataPath = null;
        if ($dbType === 'mongolite') {
            $inputMongoliteDataPath = $_POST['mongolite_data_path'] ?? ($inputCockpitRootPath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data');
            $inputMongoliteDataPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $inputMongoliteDataPath);
            $inputMongoliteDataPath = rtrim($inputMongoliteDataPath, DIRECTORY_SEPARATOR);

            if (!is_target_path_logically_within_base($inputMongoliteDataPath, $inputCockpitRootPath)) {
                throw new RuntimeException(sprintf(_l("Invalid or unsafe Mongolite data path: '%s'. Path must be absolute and within the Cockpit CMS installation path '%s'. Check for traversal attempts or non-existent parent directories."), $inputMongoliteDataPath, $inputCockpitRootPath));
            }

            $realInputMongoliteDataPath = realpath($inputMongoliteDataPath);
            if ($realInputMongoliteDataPath === false) {
                ui_message(sprintf(_l("Mongolite data path '%s' does not exist yet. Will attempt to create it."), $inputMongoliteDataPath), 'info');
            } else {
                $inputMongoliteDataPath = normalize_path_for_comparison($realInputMongoliteDataPath);
            }
        }
        foreach ($warningsToRender as $warn) {
            ui_message($warn, 'warning');
        }
        ui_message(_l('--- Starting restoration process ---'), 'info');
        ui_message(_l('Selected backup: %s', [htmlspecialchars($selectedBackupFilename)]), 'info');
        ui_message(_l('Backup source path: %s', [htmlspecialchars($backupFilePath)]), 'info');
        ui_message(_l('Cockpit CMS installation path: %s', [htmlspecialchars($inputCockpitRootPath)]), 'info');
        if ($dbType === 'mongolite') {
            ui_message(_l('Mongolite data path: %s', [htmlspecialchars($inputMongoliteDataPath)]), 'info');
        }
        $tempExtractBaseDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cockpit_restore_' . uniqid('', true);
        $tempExtractSourceDir = $tempExtractBaseDir . DIRECTORY_SEPARATOR . 'source';
        ui_message(_l('Extracting backup to temporary directory: %s', [$tempExtractSourceDir]), 'info');
        try {
            if (!is_dir($tempExtractSourceDir)) {
                if (!mkdir($tempExtractSourceDir, 0755, true) && !is_dir($tempExtractSourceDir)) {
                    throw new RuntimeException(sprintf(_l('Failed to create temporary extraction directory "%s".', $tempExtractSourceDir)));
                }
            }
            $phar = new PharData($backupFilePath);
            $phar->extractTo($tempExtractSourceDir, null, true);
            ui_message(_l('Backup extracted successfully.'), 'success');
        } catch (Exception $e) {
            throw new RuntimeException(_l('Error extracting backup: %s', [$e->getMessage()]));
        }
        ui_message(_l('Backup manifest loaded from backup file.'), 'success');
        $dbType = $manifest['database']['type'] ?? null;
        $dbDsn = $manifest['database']['dsn'] ?? null;
        $dbName = $manifest['database']['db_name'] ?? null;
        $dbDumpRelativePath = $manifest['paths']['database_dump_relative_path'] ?? 'database_dump';
        ui_message(_l('--- Restoring Files ---'), 'info');
        $extractedCockpitRoot = $tempExtractSourceDir . DIRECTORY_SEPARATOR . 'cockpit';
        if (is_dir($extractedCockpitRoot)) {
            ui_message(_l('Removing existing Cockpit CMS installation at: %s', [$inputCockpitRootPath]), 'info');
            if (normalize_path_for_comparison($inputCockpitRootPath) === normalize_path_for_comparison($realScriptDir)) {
                $cockpitItemsIterator = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($inputCockpitRootPath, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
                );
                foreach ($cockpitItemsIterator as $item) {
                    if (realpath($item->getPathname()) !== realpath(__FILE__)) {
                        @rrmdir($item->getRealPath());
                    } else {
                        ui_message(_l('Skipping deletion of restore.php itself: %s', [$item->getPathname()]), 'info');
                    }
                }
            } else {
                rrmdir($inputCockpitRootPath);
            }
            if (!is_dir($inputCockpitRootPath) && (!mkdir($inputCockpitRootPath, 0755, true) && !is_dir($inputCockpitRootPath))) {
                throw new RuntimeException(sprintf(_l('Error: Failed to create Cockpit CMS installation directory "%s".'), $inputCockpitRootPath));
            }
            ui_message(_l('Restoring Cockpit CMS files to: %s', [$inputCockpitRootPath]), 'info');
            rcopy($extractedCockpitRoot, $inputCockpitRootPath);
            chmod_recursive_dir($inputCockpitRootPath);
        } else {
            ui_message(_l('Warning: Cockpit CMS files directory not found in backup (expected: %s). Skipping Cockpit file restoration.', [$extractedCockpitRoot]), 'warning');
        }
        ui_message(_l('Restoring project root files to: %s', [SCRIPT_DIR]), 'info');
        $iterator = new DirectoryIterator($tempExtractSourceDir);
        foreach ($iterator as $item) {
            if ($item->isDot() || $item->getBasename() === 'cockpit' || $item->getBasename() === $dbDumpRelativePath || $item->getBasename() === 'backup_manifest.json') {
                continue;
            }
            $source = $item->getRealPath();
            $dest = SCRIPT_DIR . DIRECTORY_SEPARATOR . $item->getBasename();
            if (file_exists($dest)) {
                ui_message(_l("Removing existing item '%s' before restore.", [$dest]), 'info');
                if (realpath($dest) === realpath(__FILE__)) {
                    ui_message(_l('Skipping deletion of restore.php itself: %s', [$dest]), 'info');
                } else {
                    rrmdir($dest);
                }
            }
            if (is_dir($source) && !is_dir($dest) && (!mkdir($dest, 0755, true) && !is_dir($dest))) {
                throw new RuntimeException(sprintf(_l('Error: Failed to create directory "%s" for project files.'), $dest));
            }
            ui_message(_l('Restoring project file/directory: %s to %s', [$source, $dest]), 'info');
            rcopy($source, $dest);
            chmod_recursive_dir($dest);
        }
        ui_message(_l('File restoration complete.'), 'success');

        $restoredCockpitConfigPath = $inputCockpitRootPath . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.php';
        $restoredCockpitConfigDir = dirname($restoredCockpitConfigPath);

        if (!is_dir($restoredCockpitConfigDir) && (!mkdir($restoredCockpitConfigDir, 0755, true) && !is_dir($restoredCockpitConfigDir))) {
            throw new RuntimeException(sprintf(_l('Error: Failed to create config directory "%s".'), $restoredCockpitConfigDir));
        }

        ui_message(_l('Verifying and correcting Cockpit CMS configuration paths in %s.', [$restoredCockpitConfigPath]), 'info');

        $configModified = false;
        $configData = [];

        if (file_exists($restoredCockpitConfigPath)) {
            try {
                $tempConfigData = include $restoredCockpitConfigPath;
                if (is_array($tempConfigData)) {
                    $configData = $tempConfigData;
                } else {
                    ui_message(sprintf(_l('Warning: Existing config file %s is invalid or empty. Will attempt to create a default.'), $restoredCockpitConfigPath), 'warning');
                }
            } catch (Exception $e) {
                ui_message(sprintf(_l('Warning: Existing config file %s failed to load (%s). Will attempt to create a default.'), $restoredCockpitConfigPath, $e->getMessage()), 'warning');
                $configData = [];
            }
        }

        $currentCockpitRootPath = normalize_path_for_comparison($inputCockpitRootPath);
        $currentStoragePath = normalize_path_for_comparison($inputCockpitRootPath . DIRECTORY_SEPARATOR . 'storage');
        $currentTmpPath = normalize_path_for_comparison($inputCockpitRootPath . DIRECTORY_SEPARATOR . 'tmp');
        $currentUploadsPath = normalize_path_for_comparison($inputCockpitRootPath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'uploads');

        $pathsSection = $configData['paths'] ?? [];

        $initialPaths = [
            '#root:' => $currentCockpitRootPath,
            '#storage:' => $currentStoragePath,
            '#tmp:' => $currentTmpPath,
            '#uploads:' => $currentUploadsPath,
        ];

        foreach ($initialPaths as $key => $expectedPath) {
            if (!isset($pathsSection[$key]) || normalize_path_for_comparison($pathsSection[$key]) !== $expectedPath) {
                $pathsSection[$key] = $expectedPath;
                $configModified = true;
            }
        }

        $configData['paths'] = $pathsSection;

        if (!isset($configData['app.base']) || $configData['app.base'] !== '/') {
            $configData['app.base'] = '/';
            $configModified = true;
        }

        if ($configModified) {
            $configOutput = "<?php\nreturn " . var_export($configData, true) . ";\n";
            $configOutput = str_replace('array (', '[', $configOutput);
            $configOutput = str_replace(')', ']', $configOutput);
            $configOutput = str_replace('  ', '    ', $configOutput);

            if (!file_put_contents($restoredCockpitConfigPath, $configOutput)) {
                ui_message(sprintf(_l('Error: Failed to write corrected configuration to %s.'), $restoredCockpitConfigPath), 'error');
            } else {
                ui_message(sprintf(_l('Corrected Cockpit CMS configuration paths in %s.'), $restoredCockpitConfigPath), 'success');
            }
        } else {
            ui_message(sprintf(_l('No Cockpit CMS configuration changes needed in %s.'), $restoredCockpitConfigPath), 'info');
        }
        chmod_recursive_dir($restoredCockpitConfigDir);

        ui_message(_l("\n--- Restoring Database ---"), 'info');
        $extractedDbDumpPath = $tempExtractSourceDir . DIRECTORY_SEPARATOR . $dbDumpRelativePath;
        if ($dbType === 'mongolite') {
            ui_message(_l('Mongolite database restoration is implicitly handled by "Core" files restoration. No separate database dump restore needed.'), 'info');
            if (!is_dir($inputMongoliteDataPath) && (!mkdir($inputMongoliteDataPath, 0755, true) && !is_dir($inputMongoliteDataPath))) {
                ui_message(_l("Warning: Failed to ensure Mongolite data directory exists at '%s'.", [$inputMongoliteDataPath]), 'warning');
            }
            chmod_recursive_dir($inputMongoliteDataPath);
        } elseif ($dbType === 'mongodb') {
            if (!function_exists('shell_exec')) {
                ui_message(_l('Error: shell_exec function is disabled. MongoDB restoration not possible.'), 'error');
            } elseif (empty($mongo_restore_check)) {
                ui_message(_l("Error: 'mongorestore' utility not found. MongoDB restoration not possible."), 'error');
            } else {
                ui_message(_l('Restoring MongoDB database.'), 'info');
                $dbArchiveFile = $extractedDbDumpPath . DIRECTORY_SEPARATOR . 'db.archive';
                if (!file_exists($dbArchiveFile)) {
                    throw new RuntimeException(_l("MongoDB archive file not found in backup at '%s'. Skipping database restoration.", [$dbArchiveFile]));
                }
                if (empty($dbDsn) || empty($dbName)) {
                    throw new RuntimeException(_l('Database DSN or name not found in manifest. Cannot restore MongoDB.'));
                }
                ui_message(_l('  Database DSN: %s', [$dbDsn]), 'info');
                ui_message(_l('  Database Name: %s', [$dbName]), 'info');
                ui_message(_l('  Archive: %s', [$dbArchiveFile]), 'info');
                $command = sprintf(
                    'mongorestore --uri="%s" --db="%s" --archive="%s" --drop',
                    escapeshellarg($dbDsn),
                    escapeshellarg($dbName),
                    escapeshellarg($dbArchiveFile)
                );
                ui_message(_l('Executing command: %s', [$command]), 'info');
                $output = shell_exec($command . ' 2>&1');
                ui_message(_l("MongoDB restoration output:\n%s", [$output]), 'info');
                if (str_contains($output, 'Failed:') || str_contains($output, 'error:')) {
                    throw new RuntimeException(_l('Error: MongoDB restoration failed. Check the output above and ensure mongorestore utility is installed and accessible.'));
                }
                ui_message(_l('MongoDB database restored.'), 'success');
            }
        } else {
            ui_message(_l("Warning: Unsupported database type '%s' found in manifest. Skipping database restoration.", [$dbType]), 'warning');
        }
        ui_message(_l("\n--- Cleanup ---"), 'info');
        ui_message(_l('Removing temporary extraction directory: %s', [$tempExtractBaseDir]), 'info');
        rrmdir($tempExtractBaseDir);
        $cockpitCachePath = $inputCockpitRootPath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache';
        if (is_dir($cockpitCachePath)) {
            ui_message(_l("Clearing Cockpit CMS cache at '%s'...", [$cockpitCachePath]), 'info');
            rrmdir($cockpitCachePath);
            if (!mkdir($cockpitCachePath, 0755, true) && !is_dir($cockpitCachePath)) {
                ui_message(_l("Warning: Failed to recreate Cockpit CMS cache directory: '%s'.", [$cockpitCachePath]), 'warning');
            } else {
                ui_message(_l('Cockpit CMS cache cleared.'), 'success');
                chmod_recursive_dir($cockpitCachePath);
            }
        }
        ui_message(_l("\n--- Restoration Complete ---"), 'success');
        $restoreSuccess = true;
    } else {
        $manifest = read_manifest_from_archive($backupFilePath);
        $pageContent = render_confirm_restore($selectedBackupFilename, $manifest, $warningsToRender);
    }
} catch (Exception $e) {
    $globalRestoreError = _l('CRITICAL ERROR: %s', [$e->getMessage()]);
    ui_message($globalRestoreError, 'error');
} finally {
    if (isset($tempExtractBaseDir) && is_dir($tempExtractBaseDir)) {
        ui_message(_l('Final cleanup: removing temporary extraction directory: %s', [$tempExtractBaseDir]), 'info');
        @rrmdir($tempExtractBaseDir);
    }
    if ($globalRestoreError) {
        $pageContent = render_final_status(_l('Restoration failed: %s', [$globalRestoreError]), 'error', $outputBoxContentAccumulated);
    } elseif ($restoreSuccess) {
        $pageContent = render_final_status(_l('Site restored successfully!'), 'success', $outputBoxContentAccumulated);
    } elseif (empty($pageContent)) {
        $pageContent = render_final_status(_l('An unexpected error occurred during page setup or no action was taken. This might indicate that no backup was found or a critical error prevented rendering.'), 'error', $outputBoxContentAccumulated);
    }
}
session_write_close();
echo render_html_page($finalPageTitle, $pageContent);
restore_error_handler();
?>
