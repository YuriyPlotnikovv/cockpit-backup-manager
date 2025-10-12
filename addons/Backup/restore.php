<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
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
        'This action <strong>will overwrite all current files and database</strong> on your server.' => 'Это действие <strong>перезапишет все текущие файлы и базу данных</strong> на вашем сервере.',
        'It <strong>cannot be undone</strong>. Ensure you have backed up the current state of your site if necessary.' => 'Это <strong>нельзя отменить</strong>. Убедитесь, что вы сделали резервную копию текущего состояния вашего сайта, если это необходимо.',
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
        'Start Over' => 'Начать сначала',
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
        'Session data for backup manifest not found. Please restart the restore process from the beginning.' => 'Данные сессии для манифеста резервной копии не найдены. Пожалуйста, начните процесс восстановления с начала.',
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
        'Error: shell_exec function is disabled. MongoDB restoration not possible.' => 'Ошибка: Функция shell_exec отключена. Восстановление MongoDB будет невозможно.',
        "Error: 'mongorestore' utility not found. MongoDB restoration not possible." => "Ошибка: Утилита 'mongorestore' не найдена. Восстановление MongoDB будет невозможно.",
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
        '--- Starting post-restoration cleanup ---' => '--- Начало очистки после восстановления ---',
        'Final cleanup: removing temporary extraction directory: %s' => 'Окончательная очистка: удаление временной директории извлечения: %s',
        'CRITICAL ERROR: %s' => 'КРИТИЧЕСКАЯ ОШИБКА: %s',
        'Restoration failed: %s' => 'Восстановление завершилось сбоем: %s',
        'Site restored successfully!' => 'Сайт успешно восстановлен!',
        'An unexpected error occurred during page setup or no action was taken. This might indicate that no backup was found or a critical error prevented rendering.' => 'Произошла непредвиденная ошибка при настройке страницы или не было предпринято никаких действий. Это может указывать на то, что резервная копия не найдена или критическая ошибка предотвратила рендеринг.',
        'Unknown error' => 'Неизвестная ошибка',
        'Invalid Cockpit CMS installation path: \'%s\'. Path is not resolvable.' => 'Неверный путь установки Cockpit CMS: \'%s\'. Путь неразрешим.',
        'Internal Error: Script directory path \'%s\' is not resolvable.' => 'Внутренняя ошибка: Путь к директории скрипта \'%s\' неразрешим.',
        'Invalid Mongolite data path: \'%s\'. Path is not resolvable.' => 'Неверный путь к данным Mongolite: \'%s\'. Путь неразрешим.',
        'Cockpit CMS installation path \'%s\' does not exist yet. Will attempt to create it.' => 'Путь установки Cockpit CMS \'%s\' еще не существует. Будет предпринята попытка его создания.',
        'Mongolite data path \'%s\' does not exist yet. Will attempt to create it.' => 'Путь к данным Mongolite \'%s\' еще не существует. Будет предпринята попытка его создания.',
        'Invalid or unsafe Cockpit CMS installation path: \'%s\'. Path must be absolute and within the script\'s directory \'%s\'. Check for traversal attempts or non-existent parent directories.' => 'Неверный или небезопасный путь установки Cockpit CMS: \'%s\'. Путь должен быть абсолютным и находиться в директории скрипта \'%s\'. Проверьте попытки обхода или несуществующие родительские директории.',
        'Invalid or unsafe Mongolite data path: \'%s\'. Path must be absolute and within the Cockpit CMS installation path \'%s\'. Check for traversal attempts or non-existent parent directories.' => 'Неверный или небезопасный путь к данным Mongolite: \'%s\'. Путь должен быть абсолютным и находиться в пути установки Cockpit CMS \'%s\'. Проверьте попытки обхода или несуществующие родительские директории.',
        'or' => 'или',
        'Setting appropriate file permissions...' => 'Установка соответствующих прав доступа к файлам...',
        'Failed to set permissions for %s. PHP Error: %s' => 'Не удалось установить права для %s. Ошибка PHP: %s',
        'Permissions set for %s.' => 'Права установлены для %s.',
        'Permissions set for all restored files and directories.' => 'Права установлены для всех восстановленных файлов и директорий.',
        'Warning: Path not found for permission setting: %s. Skipping.' => 'Предупреждение: Путь не найден для установки прав: %s. Пропускается.',
        'Warning: Skipping inaccessible item for permission setting: %s.' => 'Предупреждение: Пропускается недоступный элемент для установки прав: %s.',
        'Failed to set permissions for directory %s. PHP Error: %s' => 'Не удалось установить права для директории %s. Ошибка PHP: %s',
        'Failed to set permissions for file %s. PHP Error: %s' => 'Не удалось установить права для файла %s. Ошибка PHP: %s',
        'Failed to set permissions for root directory %s. PHP Error: %s' => 'Не удалось установить права для корневой директории %s. Ошибка PHP: %s',
        'Error setting permissions for %s: %s' => 'Ошибка установки прав для %s: %s',
        'Error: Failed to create Cockpit CMS installation directory "%s".' => 'Ошибка: Не удалось создать директорию установки Cockpit CMS "%s".',
        'Error: Failed to create directory "%s" for project files.' => 'Ошибка: Не удалось создать директорию "%s" для файлов проекта.',
        'Restoring project file/directory: %s to %s' => 'Восстановление файла/директории проекта: %s в %s',
        'Error: Failed to create config directory "%s".' => 'Ошибка: Не удалось создать директорию конфигурации "%s".',
        'Attempting to delete backup file: %s' => 'Попытка удаления файла резервной копии: %s',
        'Backup file %s deleted successfully.' => 'Файл резервной копии %s успешно удален.',
        'Warning: Failed to delete backup file %s. Error: %s' => 'Предупреждение: Не удалось удалить файл резервной копии %s. Ошибка: %s',
        'Attempting to delete restore script: %s' => 'Попытка удаления скрипта восстановления: %s',
        'Restore script %s deleted successfully. Redirecting to Cockpit admin...' => 'Скрипт восстановления %s успешно удален. Перенаправление на страницу администрирования Cockpit...',
        'CRITICAL WARNING: Failed to delete restore script %s. Error: %s. You MUST delete this file manually!' => 'КРИТИЧЕСКОЕ ПРЕДУПРЕЖДЕНИЕ: Не удалось удалить скрипт восстановления %s. Ошибка: %s. Вы ДОЛЖНЫ удалить этот файл вручную!',
        'Failed to delete restore script %s. You MUST delete this file manually!' => 'Не удалось удалить скрипт восстановления %s. Вы ДОЛЖНЫ удалить этот файл вручную!',
        'Restoration Failed' => 'Восстановление завершилось сбоем',
        'Be sure to delete the script "restore.php" from the server after the recovery is completed.' => 'Обязательно удалите скрипт "restore.php" с сервера после завершения восстановления.',
        'Finish' => 'Завершить',
        'Are you sure you want to delete the backup file and this restore script? This action cannot be undone!' => 'Вы уверены, что хотите удалить файл резервной копии и этот скрипт восстановления? Это действие необратимо!',
        'Yes, Delete All' => 'Да, удалить все',
        'No, Cancel' => 'Нет, отмена',
];
$GLOBALS['CURRENT_LANG'] = 'en';

$acceptedLanguages = $_SERVER['HTTP_ACCEPT_LANGUAGE'] ?? 'en';
$preferredLanguages = explode(',', $acceptedLanguages);
$globalRestoreError = null;
$renderTitle = loc('Cockpit CMS Restore');
$finalPageTitle = loc('Cockpit CMS Restore');
$outputBoxContentAccumulated = '';
$pageContent = '';
$warningsToRender = [];
$restoreSuccess = false;
$tempExtractBaseDir = null;

function loadLocales($lang): bool
{
    if ($lang === 'ru' && isset($GLOBALS['LOCALES_RU'])) {
        $GLOBALS['LOCALE'] = $GLOBALS['LOCALES_RU'];
        return true;
    }
    return false;
}

function loc($key, $args = [])
{
    $translated = $GLOBALS['LOCALE'][$key] ?? $key;
    if (!empty($args)) {
        return sprintf($translated, ...$args);
    }
    return $translated;
}

function accumulateMessage($message, $type = 'info'): void
{
    global $outputBoxContentAccumulated;
    $outputBoxContentAccumulated .= "<div class='message $type'>" . nl2br(htmlspecialchars($message)) . "</div>\n";
}

function recursiveDelete($path): bool
{
    $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    if (!file_exists($path)) {
        accumulateMessage(loc('Item not found, assuming deleted: %s', [$path]), 'info');
        return true;
    }
    if (is_file($path)) {
        accumulateMessage(loc('Deleting file: %s', [$path]), 'info');
        if (!@unlink($path)) {
            $error = error_get_last();
            throw new RuntimeException(sprintf(loc('Failed to delete file "%s". PHP Error: %s'), $path, $error['message'] ?? loc('Unknown error')));
        }
        accumulateMessage(loc('File deleted: %s', [$path]), 'success');
        return true;
    }
    if (is_dir($path)) {
        accumulateMessage(loc('Attempting to delete directory: %s', [$path]), 'info');
        try {
            $items = new RecursiveIteratorIterator(
                    new RecursiveDirectoryIterator($path, RecursiveDirectoryIterator::SKIP_DOTS),
                    RecursiveIteratorIterator::CHILD_FIRST
            );
            foreach ($items as $item) {
                if ($item->isDir()) {
                    if (!@rmdir($item->getRealPath())) {
                        $error = error_get_last();
                        throw new RuntimeException(sprintf(loc('Failed to delete subdirectory "%s". PHP Error: %s'), $item->getRealPath(), $error['message'] ?? loc('Unknown error')));
                    }
                } else if (!@unlink($item->getRealPath())) {
                    $error = error_get_last();
                    throw new RuntimeException(sprintf(loc('Failed to delete file "%s". PHP Error: %s'), $item->getRealPath(), $error['message'] ?? loc('Unknown error')));
                }
            }
            if (!@rmdir($path)) {
                $error = error_get_last();
                throw new RuntimeException(sprintf(loc('Failed to delete root directory "%s". PHP Error: %s'), $path, $error['message'] ?? loc('Unknown error')));
            }
            accumulateMessage(loc('Directory deleted: %s', [$path]), 'success');
            return true;
        } catch (Exception $e) {
            throw new RuntimeException(sprintf(loc('Error during recursive directory deletion of "%s": %s'), $path, $e->getMessage()));
        }
    }
    throw new RuntimeException(sprintf(loc('Cannot delete item "%s": neither a file nor a directory.'), $path));
}

function recursiveCopy($source, $dest): void
{
    $source = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $source);
    $dest = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $dest);
    if (!file_exists($source)) {
        accumulateMessage(loc("Warning: Source path does not exist for copying: '%s'. Skipping.", [$source]), 'warning');
        return;
    }
    if (is_file($source)) {
        $destDir = dirname($dest);
        if (!is_dir($destDir)) {
            if (!mkdir($destDir, 0755, true) && !is_dir($destDir)) {
                throw new RuntimeException(sprintf(loc('Error: Failed to create parent directory "%s" for file "%s".'), $destDir, $dest));
            }
            accumulateMessage(loc('Parent directory created: %s', [$destDir]), 'info');
        }
        if (!@copy($source, $dest)) {
            $error = error_get_last();
            throw new RuntimeException(sprintf(loc('Error: Failed to copy file from "%s" to "%s". PHP Error: %s'), $source, $dest, $error['message'] ?? loc('Unknown error')));
        }
        accumulateMessage(loc('File copied: %s to %s', [$source, $dest]), 'info');
    } elseif (is_dir($source)) {
        if (!is_dir($dest)) {
            if (!mkdir($dest, 0755, true) && !is_dir($dest)) {
                throw new RuntimeException(sprintf(loc('Error: Failed to create directory "%s" for copying.'), $dest));
            }
            accumulateMessage(loc('Directory created: %s', [$dest]), 'info');
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
                        throw new RuntimeException(sprintf(loc('Error: Failed to create subdirectory "%s" for copying.'), $path));
                    }
                    accumulateMessage(loc('Subdirectory created: %s', [$path]), 'info');
                }
            } else if (!@copy($item->getRealPath(), $path)) {
                $error = error_get_last();
                throw new RuntimeException(sprintf(loc('Error: Failed to copy file from "%s" to "%s". PHP Error: %s'), $item->getRealPath(), $path, $error['message'] ?? loc('Unknown error')));
            }
        }
        accumulateMessage(loc('Directory copied: %s to %s', [$source, $dest]), 'info');
    } else {
        accumulateMessage(loc("Warning: Source '%s' is neither a file nor a directory. Skipping.", [$source]), 'warning');
    }
}

function scanForBackups($directory): array
{
    $directory = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $directory);
    $backups = [];
    if (!is_dir($directory)) {
        accumulateMessage(loc("Warning: Backup directory '%s' not found. Cannot scan for backups.", [$directory]), 'warning');
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

function readDataFromArchive($archivePath)
{
    $archivePath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $archivePath);
    if (!file_exists($archivePath)) {
        throw new RuntimeException(loc("Backup archive not found: '%s'.", [$archivePath]));
    }
    $tempManifestDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cockpit_manifest_extract_' . uniqid('', true);
    if (!mkdir($tempManifestDir, 0755, true) && !is_dir($tempManifestDir)) {
        throw new RuntimeException(loc('Failed to create temporary directory for manifest extraction.'));
    }
    accumulateMessage(loc("Extracting manifest from '%s' to '%s'.", [$archivePath, $tempManifestDir]));
    try {
        $phar = new PharData($archivePath);
        $phar->extractTo($tempManifestDir, 'backup_manifest.json', true);
        $manifestFilePath = $tempManifestDir . DIRECTORY_SEPARATOR . 'backup_manifest.json';
        if (!file_exists($manifestFilePath)) {
            throw new Exception(loc('Manifest file not found in the archive after extraction.'));
        }
        $manifest = json_decode(file_get_contents($manifestFilePath), true, 512, JSON_THROW_ON_ERROR);
        accumulateMessage(loc('Manifest extracted and parsed successfully.'), 'success');
        return $manifest;
    } catch (Exception $e) {
        throw new RuntimeException(loc('Error reading manifest from archive: %s', [$e->getMessage()]));
    } finally {
        if (is_dir($tempManifestDir)) {
            accumulateMessage(loc('Cleaning up temporary manifest directory: %s', [$tempManifestDir]), 'info');
            @recursiveDelete($tempManifestDir);
        }
    }
}

function renderHtmlPage($title, $bodyContent): bool|string
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

function renderConfirmRestore($backupFilename, $manifest, $warnings = []): bool|string
{
    ob_start();
    $displayDsn = $manifest['database']['dsn'] ?? 'N/A';
    if (str_starts_with($displayDsn, 'mongodb://') && str_contains($displayDsn, '@')) {
        $displayDsn = preg_replace('/(?<=:\/\/.*:).*?(?=@)/', '***', $displayDsn);
    }
    $defaultCockpitPath = __DIR__ . DIRECTORY_SEPARATOR . 'cockpit';
    $defaultMongoliteDataPath = $defaultCockpitPath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data';
    ?>
    <h1><?= loc('Restore Confirmation') ?></h1>
    <div class="warning">
        <strong><?= loc('WARNING!') ?></strong> <?= loc('You are about to restore your site from backup') ?>
        "<strong><?= htmlspecialchars($backupFilename) ?></strong>".
        <br><?= loc('This action <strong>will overwrite all current files and database</strong> on your server.') ?>
        <br><?= loc('It <strong>cannot be undone</strong>. Ensure you have backed up the current state of your site if necessary.') ?>
    </div>
    <?php if (!empty($warnings)): ?>
        <?php foreach ($warnings as $warn): ?>
            <div class="message warning"><?= htmlspecialchars($warn) ?></div>
        <?php endforeach; ?>
    <?php endif; ?>
    <h3><?= loc('Backup Details:') ?></h3>
    <ul>
        <li><strong><?= loc('Filename:') ?></strong> <?= htmlspecialchars($backupFilename) ?></li>
        <li>
            <strong><?= loc('Cockpit Version:') ?></strong> <?= htmlspecialchars($manifest['cockpit_version'] ?? 'N/A') ?>
        </li>
        <li><strong><?= loc('Creation Date:') ?></strong> <?= date('Y-m-d H:i:s', $manifest['timestamp'] ?? time()) ?>
        </li>
        <li><strong><?= loc('DB Type:') ?></strong> <?= htmlspecialchars($manifest['database']['type'] ?? 'N/A') ?></li>
        <li>
            <strong><?= loc('DB Name (MongoDB):') ?></strong> <?= htmlspecialchars($manifest['database']['db_name'] ?? 'N/A') ?>
        </li>
        <li><strong><?= loc('DB DSN:') ?></strong> <code><?= htmlspecialchars($displayDsn) ?></code> <span
                    style="font-size:0.8em;color:#888;">(<?= loc('may contain sensitive data') ?>)</span></li>
    </ul>
    <form method="POST" action="?state=perform_restore&backup=<?= urlencode($backupFilename) ?>">
        <div class="form-group">
            <label for="cockpit_path"><?= loc('Path to Cockpit CMS installation (where index.php is):') ?></label>
            <input type="text" id="cockpit_path" name="cockpit_path"
                   value="<?= htmlspecialchars($defaultCockpitPath) ?>" required>
        </div>
        <?php if (($manifest['database']['type'] ?? null) === 'mongolite'): ?>
            <div class="form-group">
                <label
                        for="mongolite_data_path"><?= loc('Path to Mongolite data folder (e.g., /cockpit/storage/data):') ?></label>
                <input type="text" id="mongolite_data_path" name="mongolite_data_path"
                       value="<?= htmlspecialchars($defaultMongoliteDataPath) ?>"
                       required>
            </div>
        <?php endif; ?>
        <button type="submit" class="button-danger" name="action"
                value="restore_confirm"><?= loc('Start Restoration') ?></button>
        <a href="?" class="back-link"><?= loc('Cancel') ?></a>
    </form>
    <?php return ob_get_clean();
}

function renderFinalStatus($message, $type = 'success', $outputContent = '', $backupFilePath = null): bool|string
{
    ob_start(); ?>
    <h1><?= loc('Restoration Complete') ?></h1>
    <div class="message <?= htmlspecialchars($type) ?>"><?= nl2br(htmlspecialchars($message)) ?></div>
    <?php if ($outputContent): ?>
        <h2><?= loc('Restoration Log:') ?></h2>
        <div class="output-box" id="restore-output">
            <?= $outputContent ?>
        </div>
    <?php endif; ?>
    <div class="warning">
        <strong><?= loc('Be sure to delete the script "restore.php" from the server after the recovery is completed.') ?></strong>
    </div>
    <div style="margin-top: 20px;">
        <a href="?" class="back-link"><?= loc('Start Over') ?></a>
        <?php if ($backupFilePath && file_exists($backupFilePath)): ?>
            <form method="POST" action="?state=delete_files&backup=<?= urlencode(basename($backupFilePath)) ?>"
                  style="display:inline-block; margin-left: 20px;">
                <button type="submit" class="button-danger"
                        onclick="return confirm('<?= loc('Are you sure you want to delete the backup file and this restore script? This action cannot be undone!') ?>')">
                    <?= loc('Finish') ?>
                </button>
            </form>
        <?php endif; ?>
    </div>
    <?php return ob_get_clean();
}

function normalizePath(string $path): string
{
    $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    return rtrim($path, DIRECTORY_SEPARATOR);
}

function comparePath(string $targetPath, string $basePath): bool
{
    $normalizedTargetPath = normalizePath($targetPath);
    $normalizedBasePath = normalizePath($basePath);
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

function setFullPermissions(string $path, int $dirPermissions = 0755, int $filePermissions = 0644): void
{
    $path = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $path);
    if (!file_exists($path)) {
        accumulateMessage(sprintf(loc('Warning: Path not found for permission setting: %s. Skipping.'), $path), 'warning');
        return;
    }
    accumulateMessage(sprintf(loc('Setting appropriate file permissions for %s...'), $path), 'info');
    try {
        if (is_file($path)) {
            if (!@chmod($path, $filePermissions)) {
                $error = error_get_last();
                accumulateMessage(sprintf(loc('Failed to set permissions for %s. PHP Error: %s'), $path, $error['message'] ?? loc('Unknown error')), 'error');
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
                accumulateMessage(sprintf(loc('Warning: Skipping inaccessible item for permission setting: %s.'), $item->getPathname()), 'warning');
                continue;
            }
            if ($item->isDir()) {
                if (!@chmod($currentPath, $dirPermissions)) {
                    $error = error_get_last();
                    accumulateMessage(sprintf(loc('Failed to set permissions for directory %s. PHP Error: %s'), $currentPath, $error['message'] ?? loc('Unknown error')), 'error');
                }
            } elseif ($item->isFile()) {
                if (!@chmod($currentPath, $filePermissions)) {
                    $error = error_get_last();
                    accumulateMessage(sprintf(loc('Failed to set permissions for file %s. PHP Error: %s'), $currentPath, $error['message'] ?? loc('Unknown error')), 'error');
                }
            }
        }
        if (!@chmod($path, $dirPermissions)) {
            $error = error_get_last();
            accumulateMessage(sprintf(loc('Failed to set permissions for root directory %s. PHP Error: %s'), $path, $error['message'] ?? loc('Unknown error')), 'error');
        } else {
            accumulateMessage(sprintf(loc('Permissions set for %s.'), $path), 'success');
        }
    } catch (Exception $e) {
        accumulateMessage(sprintf(loc('Error setting permissions for %s: %s'), $path, $e->getMessage()), 'error');
    }
}

foreach ($preferredLanguages as $lang) {
    $lang = trim(explode(';', $lang)[0]);
    $langShort = strtolower(substr($lang, 0, 2));
    if ($langShort === 'ru') {
        $GLOBALS['CURRENT_LANG'] = 'ru';
        loadLocales('ru');
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

try {
    if (!class_exists('PharData')) {
        throw new RuntimeException(loc('PHP Phar extension is not enabled. It is required for backup/restore operations.'));
    }
    $mongo_restore_check = function_exists('shell_exec') ? shell_exec('command -v mongorestore 2>/dev/null') : null;
    $mongo_dump_check = function_exists('shell_exec') ? shell_exec('command -v mongodump 2>/dev/null') : null;
    if (!function_exists('shell_exec')) {
        $warningsToRender[] = loc('shell_exec function is disabled. MongoDB restoration will not be possible.');
    } elseif (empty($mongo_restore_check)) {
        $warningsToRender[] = loc("'mongorestore' utility not found in PATH. MongoDB restoration will not be possible.");
    } elseif (empty($mongo_dump_check)) {
        $warningsToRender[] = loc("'mongodump' utility not found in PATH. MongoDB backup/restore might face issues.");
    }
    $state = $_GET['state'] ?? 'confirm_restore';
    $selectedBackupFilename = (isset($_GET['backup']) && $_GET['backup'] !== '') ? $_GET['backup'] : null;
    $action = $_POST['action'] ?? '';
    $backupDir = __DIR__;
    $availableBackups = scanForBackups($backupDir);
    if (empty($availableBackups)) {
        throw new RuntimeException(sprintf(loc("No backup files (.tar.gz) found in '%s'. Please place a backup file in this directory."), htmlspecialchars($backupDir)));
    }
    $chosenBackupData = null;
    if ($selectedBackupFilename !== null) {
        if (!preg_match('/^[a-zA-Z0-9._-]+\.tar\.gz$/', $selectedBackupFilename) || str_contains($selectedBackupFilename, '..') || str_contains($selectedBackupFilename, '/')) {
            throw new RuntimeException(loc('Invalid backup filename specified.'));
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
        throw new RuntimeException(loc('Internal Error: Chosen backup data could not be resolved, despite backups being found.'));
    }
    $backupFilePath = $chosenBackupData['path'];
    $realBackupFilePath = realpath($backupFilePath);
    $realBackupDir = realpath($backupDir);
    if ($realBackupFilePath === false || $realBackupDir === false || !str_starts_with($realBackupFilePath, $realBackupDir)) {
        throw new RuntimeException(sprintf(loc("Security Error: Resolved backup file '%s' is not within the expected backup directory '%s'."), $backupFilePath, $backupDir));
    }
    if (!file_exists($backupFilePath) || !is_file($backupFilePath)) {
        throw new RuntimeException(sprintf(loc("Resolved backup file '%s' does not exist or is not a file. This indicates a filesystem issue or incorrect path resolving."), $backupFilePath));
    }

    if ($state === 'delete_files' && $_SERVER['REQUEST_METHOD'] === 'POST') {
        $backupFileToDelete = $backupFilePath;
        $restoreScriptPath = __FILE__;

        accumulateMessage(loc('--- Starting post-restoration cleanup ---'), 'info');

        accumulateMessage(sprintf(loc('Attempting to delete backup file: %s'), htmlspecialchars($backupFileToDelete)), 'info');
        try {
            recursiveDelete($backupFileToDelete);
            accumulateMessage(sprintf(loc('Backup file %s deleted successfully.'), htmlspecialchars($backupFileToDelete)), 'success');
        } catch (RuntimeException $e) {
            accumulateMessage(sprintf(loc('Warning: Failed to delete backup file %s. Error: %s'), htmlspecialchars($backupFileToDelete), $e->getMessage()), 'warning');
        }

        accumulateMessage(sprintf(loc('Attempting to delete restore script: %s'), htmlspecialchars($restoreScriptPath)), 'info');
        if (@unlink($restoreScriptPath)) {
            accumulateMessage(sprintf(loc('Restore script %s deleted successfully. Redirecting to Cockpit admin...'), htmlspecialchars($restoreScriptPath)), 'success');
            session_write_close();
            header('Location: /cockpit');
            exit;
        } else {
            $error = error_get_last();
            $errorMessage = sprintf(loc('CRITICAL WARNING: Failed to delete restore script %s. Error: %s. You MUST delete this file manually!'), htmlspecialchars($restoreScriptPath), $error['message'] ?? loc('Unknown error'));
            accumulateMessage($errorMessage, 'error');
            $globalRestoreError = loc('Failed to delete restore script %s. You MUST delete this file manually!', [htmlspecialchars($restoreScriptPath)]);
            $pageContent = renderFinalStatus(loc('Restoration Failed'), 'error', $outputBoxContentAccumulated, $backupFilePath);
        }

    } else if ($state === 'perform_restore' && $action === 'restore_confirm') {
        $inputCockpitRootPath = $_POST['cockpit_path'] ?? '';
        $inputCockpitRootPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $inputCockpitRootPath);
        $inputCockpitRootPath = rtrim($inputCockpitRootPath, DIRECTORY_SEPARATOR);
        if (empty($inputCockpitRootPath)) {
            throw new RuntimeException(loc('Cockpit CMS installation path cannot be empty.'));
        }
        $realScriptDir = realpath(__DIR__);
        if ($realScriptDir === false) {
            throw new RuntimeException(sprintf(loc("Internal Error: Script directory path '%s' is not resolvable."), __DIR__));
        }
        $normalizedRealScriptDir = normalizePath($realScriptDir);
        if (!comparePath($inputCockpitRootPath, $normalizedRealScriptDir)) {
            throw new RuntimeException(sprintf(loc("Invalid or unsafe Cockpit CMS installation path: '%s'. Path must be absolute and within the script's directory '%s'. Check for traversal attempts or non-existent parent directories."), $inputCockpitRootPath, $normalizedRealScriptDir));
        }
        $realInputCockpitRootPath = realpath($inputCockpitRootPath);
        if ($realInputCockpitRootPath === false) {
            accumulateMessage(sprintf(loc("Cockpit CMS installation path '%s' does not exist yet. Will attempt to create it."), $inputCockpitRootPath), 'info');
        } else {
            $inputCockpitRootPath = normalizePath($realInputCockpitRootPath);
        }
        $manifest = readDataFromArchive($backupFilePath);
        $dbType = $manifest['database']['type'] ?? null;
        $inputMongoliteDataPath = null;
        if ($dbType === 'mongolite') {
            $inputMongoliteDataPath = $_POST['mongolite_data_path'] ?? ($inputCockpitRootPath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'data');
            $inputMongoliteDataPath = str_replace(['/', '\\'], DIRECTORY_SEPARATOR, $inputMongoliteDataPath);
            $inputMongoliteDataPath = rtrim($inputMongoliteDataPath, DIRECTORY_SEPARATOR);
            if (!comparePath($inputMongoliteDataPath, $inputCockpitRootPath)) {
                throw new RuntimeException(sprintf(loc("Invalid or unsafe Mongolite data path: '%s'. Path must be absolute and within the Cockpit CMS installation path '%s'. Check for traversal attempts or non-existent parent directories."), $inputMongoliteDataPath, $inputCockpitRootPath));
            }
            $realInputMongoliteDataPath = realpath($inputMongoliteDataPath);
            if ($realInputMongoliteDataPath === false) {
                accumulateMessage(sprintf(loc("Mongolite data path '%s' does not exist yet. Will attempt to create it."), $inputMongoliteDataPath), 'info');
            } else {
                $inputMongoliteDataPath = normalizePath($realInputMongoliteDataPath);
            }
        }
        foreach ($warningsToRender as $warn) {
            accumulateMessage($warn, 'warning');
        }
        accumulateMessage(loc('--- Starting restoration process ---'), 'info');
        accumulateMessage(loc('Selected backup: %s', [htmlspecialchars($selectedBackupFilename)]), 'info');
        accumulateMessage(loc('Backup source path: %s', [htmlspecialchars($backupFilePath)]), 'info');
        accumulateMessage(loc('Cockpit CMS installation path: %s', [htmlspecialchars($inputCockpitRootPath)]), 'info');
        if ($dbType === 'mongolite') {
            accumulateMessage(loc('Mongolite data path: %s', [htmlspecialchars($inputMongoliteDataPath)]), 'info');
        }
        $tempExtractBaseDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'cockpit_restore_' . uniqid('', true);
        $tempExtractSourceDir = $tempExtractBaseDir . DIRECTORY_SEPARATOR . 'source';
        accumulateMessage(loc('Extracting backup to temporary directory: %s', [$tempExtractSourceDir]), 'info');
        try {
            if (!is_dir($tempExtractSourceDir)) {
                if (!mkdir($tempExtractSourceDir, 0755, true) && !is_dir($tempExtractSourceDir)) {
                    throw new RuntimeException(sprintf(loc('Failed to create temporary extraction directory "%s".', $tempExtractSourceDir)));
                }
            }
            $phar = new PharData($backupFilePath);
            $phar->extractTo($tempExtractSourceDir, null, true);
            accumulateMessage(loc('Backup extracted successfully.'), 'success');
        } catch (Exception $e) {
            throw new RuntimeException(loc('Error extracting backup: %s', [$e->getMessage()]));
        }
        accumulateMessage(loc('Backup manifest loaded from backup file.'), 'success');
        $dbType = $manifest['database']['type'] ?? null;
        $dbDsn = $manifest['database']['dsn'] ?? null;
        $dbName = $manifest['database']['db_name'] ?? null;
        $dbDumpRelativePath = $manifest['paths']['database_dump_relative_path'] ?? 'database_dump';
        accumulateMessage(loc('--- Restoring Files ---'));
        $extractedCockpitRoot = $tempExtractSourceDir . DIRECTORY_SEPARATOR . 'cockpit';
        if (is_dir($extractedCockpitRoot)) {
            accumulateMessage(loc('Removing existing Cockpit CMS installation at: %s', [$inputCockpitRootPath]), 'info');
            if (normalizePath($inputCockpitRootPath) === normalizePath($realScriptDir)) {
                $cockpitItemsIterator = new RecursiveIteratorIterator(
                        new RecursiveDirectoryIterator($inputCockpitRootPath, RecursiveDirectoryIterator::SKIP_DOTS),
                        RecursiveIteratorIterator::CHILD_FIRST
                );
                foreach ($cockpitItemsIterator as $item) {
                    if (realpath($item->getPathname()) !== realpath(__FILE__)) {
                        @recursiveDelete($item->getRealPath());
                    } else {
                        accumulateMessage(loc('Skipping deletion of restore.php itself: %s', [$item->getPathname()]), 'info');
                    }
                }
            } else {
                recursiveDelete($inputCockpitRootPath);
            }
            if (!is_dir($inputCockpitRootPath) && (!mkdir($inputCockpitRootPath, 0755, true) && !is_dir($inputCockpitRootPath))) {
                throw new RuntimeException(sprintf(loc('Error: Failed to create Cockpit CMS installation directory "%s".'), $inputCockpitRootPath));
            }
            accumulateMessage(loc('Restoring Cockpit CMS files to: %s', [$inputCockpitRootPath]), 'info');
            recursiveCopy($extractedCockpitRoot, $inputCockpitRootPath);
            setFullPermissions($inputCockpitRootPath);
        } else {
            accumulateMessage(loc('Warning: Cockpit CMS files directory not found in backup (expected: %s). Skipping Cockpit file restoration.', [$extractedCockpitRoot]), 'warning');
        }
        accumulateMessage(loc('Restoring project root files to: %s', [__DIR__]), 'info');
        $iterator = new DirectoryIterator($tempExtractSourceDir);
        foreach ($iterator as $item) {
            if ($item->isDot() || $item->getBasename() === 'cockpit' || $item->getBasename() === $dbDumpRelativePath || $item->getBasename() === 'backup_manifest.json') {
                continue;
            }
            $source = $item->getRealPath();
            $dest = __DIR__ . DIRECTORY_SEPARATOR . $item->getBasename();
            if (file_exists($dest)) {
                accumulateMessage(loc("Removing existing item '%s' before restore.", [$dest]), 'info');
                if (realpath($dest) === realpath(__FILE__)) {
                    accumulateMessage(loc('Skipping deletion of restore.php itself: %s', [$dest]), 'info');
                } else {
                    recursiveDelete($dest);
                }
            }
            if (is_dir($source) && !is_dir($dest) && (!mkdir($dest, 0755, true) && !is_dir($dest))) {
                throw new RuntimeException(sprintf(loc('Error: Failed to create directory "%s" for project files.'), $dest));
            }
            accumulateMessage(loc('Restoring project file/directory: %s to %s', [$source, $dest]), 'info');
            recursiveCopy($source, $dest);
            setFullPermissions($dest);
        }
        accumulateMessage(loc('File restoration complete.'), 'success');
        accumulateMessage(loc("\n--- Restoring Database ---"), 'info');
        $extractedDbDumpPath = $tempExtractSourceDir . DIRECTORY_SEPARATOR . $dbDumpRelativePath;
        if ($dbType === 'mongolite') {
            accumulateMessage(loc('Mongolite database restoration is implicitly handled by "Core" files restoration. No separate database dump restore needed.'), 'info');
            if (!is_dir($inputMongoliteDataPath) && (!mkdir($inputMongoliteDataPath, 0755, true) && !is_dir($inputMongoliteDataPath))) {
                accumulateMessage(loc("Warning: Failed to ensure Mongolite data directory exists at '%s'.", [$inputMongoliteDataPath]), 'warning');
            }
            setFullPermissions($inputMongoliteDataPath);
        } elseif ($dbType === 'mongodb') {
            if (!function_exists('shell_exec')) {
                accumulateMessage(loc('Error: shell_exec function is disabled. MongoDB restoration not possible.'), 'error');
            } elseif (empty($mongo_restore_check)) {
                accumulateMessage(loc("Error: 'mongorestore' utility not found. MongoDB restoration not possible."), 'error');
            } else {
                accumulateMessage(loc('Restoring MongoDB database.'), 'info');
                $dbArchiveFile = $extractedDbDumpPath . DIRECTORY_SEPARATOR . 'db.archive';
                if (!file_exists($dbArchiveFile)) {
                    throw new RuntimeException(loc("MongoDB archive file not found in backup at '%s'. Skipping database restoration.", [$dbArchiveFile]));
                }
                if (empty($dbDsn) || empty($dbName)) {
                    throw new RuntimeException(loc('Database DSN or name not found in manifest. Cannot restore MongoDB.'));
                }
                accumulateMessage(loc('  Database DSN: %s', [$dbDsn]), 'info');
                accumulateMessage(loc('  Database Name: %s', [$dbName]), 'info');
                accumulateMessage(loc('  Archive: %s', [$dbArchiveFile]), 'info');
                $command = sprintf(
                        'mongorestore --uri="%s" --db="%s" --archive="%s" --drop',
                        escapeshellarg($dbDsn),
                        escapeshellarg($dbName),
                        escapeshellarg($dbArchiveFile)
                );
                accumulateMessage(loc('Executing command: %s', [$command]), 'info');
                $output = shell_exec($command . ' 2>&1');
                accumulateMessage(loc("MongoDB restoration output:\n%s", [$output]), 'info');
                if (str_contains($output, 'Failed:') || str_contains($output, 'error:')) {
                    throw new RuntimeException(loc('Error: MongoDB restoration failed. Check the output above and ensure mongorestore utility is installed and accessible.'));
                }
                accumulateMessage(loc('MongoDB database restored.'), 'success');
            }
        } else {
            accumulateMessage(loc("Warning: Unsupported database type '%s' found in manifest. Skipping database restoration.", [$dbType]), 'warning');
        }
        accumulateMessage(loc("\n--- Cleanup ---"), 'info');
        accumulateMessage(loc('Removing temporary extraction directory: %s', [$tempExtractBaseDir]), 'info');
        recursiveDelete($tempExtractBaseDir);
        $cockpitCachePath = $inputCockpitRootPath . DIRECTORY_SEPARATOR . 'storage' . DIRECTORY_SEPARATOR . 'cache';
        if (is_dir($cockpitCachePath)) {
            accumulateMessage(loc("Clearing Cockpit CMS cache at '%s'...", [$cockpitCachePath]));
            recursiveDelete($cockpitCachePath);
            if (!mkdir($cockpitCachePath, 0755, true) && !is_dir($cockpitCachePath)) {
                accumulateMessage(loc("Warning: Failed to recreate Cockpit CMS cache directory: '%s'.", [$cockpitCachePath]), 'warning');
            } else {
                accumulateMessage(loc('Cockpit CMS cache cleared.'), 'success');
                setFullPermissions($cockpitCachePath);
            }
        }
        accumulateMessage(loc("\n--- Restoration Complete ---"), 'success');
        $restoreSuccess = true;
        $pageContent = renderFinalStatus(loc('Site restored successfully!'), 'success', $outputBoxContentAccumulated, $backupFilePath);
    } else {
        $manifest = readDataFromArchive($backupFilePath);
        $pageContent = renderConfirmRestore($selectedBackupFilename, $manifest, $warningsToRender);
    }
} catch (Exception $e) {
    $globalRestoreError = loc('CRITICAL ERROR: %s', [$e->getMessage()]);
    accumulateMessage($globalRestoreError, 'error');
} finally {
    if (isset($tempExtractBaseDir) && is_dir($tempExtractBaseDir)) {
        accumulateMessage(loc('Final cleanup: removing temporary extraction directory: %s', [$tempExtractBaseDir]), 'info');
        @recursiveDelete($tempExtractBaseDir);
    }
    if ($globalRestoreError) {
        $finalPageTitle = loc('Restoration Failed');
        $pageContent = renderFinalStatus(loc('Restoration failed: %s', [$globalRestoreError]), 'error', $outputBoxContentAccumulated);
    }
    if (empty($pageContent)) {
        $finalPageTitle = loc('Restoration Failed');
        $pageContent = renderFinalStatus(loc('An unexpected error occurred during page setup or no action was taken. This might indicate that no backup was found or a critical error prevented rendering.'), 'error', $outputBoxContentAccumulated);
    }
}

session_write_close();
echo renderHtmlPage($finalPageTitle, $pageContent);
restore_error_handler();
?>
