# Cockpit Backup Manager

[🇬🇧 English](#english) | [🇷🇺 Русский](#русский)

---

## English

### Cockpit Backup Manager

A module for Cockpit CMS designed to create, manage, and restore backups of your system and data.

---

## Table of Contents

* [Features](#features)
* [Requirements](#requirements)
* [Installation](#installation)
* [Configuration](#configuration)
* [Usage](#usage)
    * [Creating and Managing Backups (via Admin Panel)](#creating-and-managing-backups-via-admin-panel)
    * [Restoring to an Empty Server (using `restore.php`)](#restoring-to-an-empty-server-using-restorephp)
* [Localization](#localization)
* [Troubleshooting](#troubleshooting)
* [License](#license)

---

## Features

* **Flexible Backup**: Create `.tar.gz` archives, including:
    * **Cockpit CMS Core**: Cockpit system files.
    * **Website Files**: All project files, except the `cockpit` folder.
    * **Database**:
        * For **Mongolite**: Database data is copied along with Cockpit core files (stored in `cockpit/storage/data`).
        * For **MongoDB**: A separate database dump is created using the `mongodump` utility.
* **Exclusions**: Ability to specify paths (files or directories) to exclude from the backup via module settings.
* **Backup Management**:
    * View a list of created backups with size and date information.
    * Download backups via the admin panel.
    * Delete backups via the admin panel.
* **Restoration from Backup**:
    * Restoration functionality via the admin panel (for a working server).
    * **Special script `restore.php`** for restoring to a new or "empty" server (outside the admin panel).

---

## Requirements

### Base:

*   **Cockpit CMS**: v2.x (the module was developed for this version).
*   **PHP**: v8.1+ (or higher, depending on Cockpit CMS requirements).

### For restoration via `restore.php` script:

*   **PHP `Phar` Extension**: Must be enabled in PHP configuration.
*   **For MongoDB backup/restore**:
    *   The `shell_exec` function must be enabled in PHP configuration.
    *   The `mongodump` and `mongorestore` utilities must be installed on the server and available via PATH.

---

## Installation

1. Copy the `Backup` folder from this repository to `your_cockpit_root/addons/`.
   If necessary, copy the translation file to `your_cockpit_root/config/`.
   Your folder structure should look approximately like this:
   ```
   your_project_root/
   ├── cockpit/
   │   └── addons/
   │   │   └── Backup/
   │   │       ├── Controller/
   │   │       │   └── ...
   │   │       ├── Helper/
   │   │       │   └── ...
   │   │       ├── views/
   │   │       │   └── ...
   │   │       ├── admin.php
   │   │       ├── bootstrap.php
   │   │       ├── icon.svg
   │   │       └── restore.php
   │   └── config/
   │       └── i18n/
   │           ├── ru/
   │           │   └── Backup.php
   ```
2. Log in to the Cockpit admin panel.
3. Go to `Settings` -> `Backup`.

---

## Configuration

Module configuration is done via the Cockpit admin panel:

1. In the admin panel, go to `Settings` -> `Backup`.
2. On the **"Backup Settings"** tab:
    * **Backup Configuration**: Select which parts of the system will be included in the backup (Cockpit Core, Website
      Files, Database).
    * **Excluded Paths**: Specify paths (relative to the site root) that should be excluded from the backup. For
      example: `my_temp_folder`, `cockpit/cache/some_large_files`.
      **Additional configuration for backup storage path (via `cockpit/config/config.php` file):**
      By default, backups are stored in `your_cockpit_root/backups`. You can change this by adding the following section
      to `cockpit/config/config.php`:

```php
<?php
return [
    'backup' => [
        'config' => [
            'backup_path' => '/path_to_your_custom_backups_folder',
        ],
    ],
];
```

---

## Usage

### Creating and Managing Backups (via Admin Panel)

1. Go to `Settings` -> `Backup`.
2. On the **"List of backups"** tab:
    * You will see a list of all previously created backups.
    * For each backup, the following actions are available:
        * **Download** (`download`): Downloads the backup archive to your computer.
        * **Restore** (`restore`): Initiates the restoration process from the current server. **ATTENTION:** This will
          overwrite existing files and the database.
        * **Delete** (`delete`): Deletes the backup file.
    * The **"Create a backup"** button starts the process of creating a new backup according to current settings.
    * The **"Refresh list of backups"** button updates the list of backup files.

### Restoring to an Empty Server (using `restore.php`)

This method is intended for situations where Cockpit CMS is not functioning or is being installed on a new server.
**CRITICALLY IMPORTANT:**

1. **Placement of the `restore.php` script**:
    * Download the `restore.php` file from the admin panel (under "Backup List", link "Download restoration file:
      restore.php").
    * **Upload this `restore.php` to the ROOT DIRECTORY OF YOUR WEBSITE**.
    * For example, if your domain `example.com` points to `public_html/`, then `restore.php` should be in
      `public_html/`. The `cockpit` folder after restoration will be in `public_html/cockpit`.
2. **Placement of the backup file**:
    * Upload your `.tar.gz` backup file to **the same directory** as the `restore.php` script.
3. **Initiating the restoration process**:
    * Open `restore.php` in your browser (e.g., `https://yourdomain.ru/restore.php`).
    * The script will automatically detect backup files in the current directory and select the most recent one.
    * On the restoration confirmation page:
        * Check the backup details.
        * **Make sure the paths to your Cockpit CMS installation and Mongolite data folder are specified correctly.**
          The script will attempt to suggest default paths based on its location, but they may require adjustment.
        * Click **"Start Restoration"**.
    * The script will perform the following actions:
        * Extract the archive to a temporary directory.
        * Delete existing files in target directories (Cockpit, project files) before copying from the backup.
        * Copy Cockpit core files and project files.
        * **Set necessary permissions** for all restored files and directories.
        * Restore the database (Mongolite by copying files, MongoDB via `mongorestore`).
        * Clear Cockpit CMS cache.
        * Delete temporary files.
    * After successful restoration, the page will display:
        * A link **"Start Over"**.
        * A button **"Delete restoration files and go to site"**.

**EXTREMELY IMPORTANT WARNING:**

* **After successful restoration, YOU MUST IMMEDIATELY DELETE the `restore.php` file from the server.** This is a
  powerful tool that can be used by malicious actors to completely overwrite your site. Use the "Delete backup file and
  restoration script" button for safe cleanup. If automatic deletion fails, remove the files manually.

---

## License

The module is distributed under the [GNU Affero General Public License v3 (AGPLv3)](https://www.gnu.org/licenses/agpl-3.0.html).

---

## Contacts

Author: Yuriy Plotnikov  
Website: https://yuriyplotnikovv.ru

---

## Русский

### Cockpit Backup Manager

Модуль для Cockpit CMS, предназначенный для создания, управления и восстановления резервных копий вашей системы и
данных.

---

## Содержание

* [Возможности](#возможности)
* [Требования](#требования)
* [Установка](#установка)
* [Конфигурация](#конфигурация)
* [Использование](#использование)
    * [Создание и управление резервными копиями (через админ-панель)](#создание-и-управление-резервными-копиями-через-админ-панель)
    * [Восстановление на пустой сервер (используя `restore.php`)](#восстановление-на-пустой-сервер-используя-restorephp)
* [Локализация](#локализация)
* [Устранение неисправностей](#устранение-неисправностей)
* [Лицензия](#лицензия)

---

## Возможности

* **Гибкое резервное копирование**: Создание `.tar.gz` архивов, включающих:
    * **Ядро Cockpit CMS**: Системные файлы Cockpit.
    * **Файлы сайта**: Все файлы вашего проекта, кроме папки `cockpit`.
    * **База данных**:
        * Для **Mongolite**: Данные базы данных копируются вместе с файлами ядра Cockpit (хранятся в
          `cockpit/storage/data`).
        * Для **MongoDB**: Создается отдельный дамп базы данных с использованием утилиты `mongodump`.
* **Исключения**: Возможность указывать пути (файлы или директории) для исключения из резервной копии через настройки
  модуля.
* **Управление резервными копиями**:
    * Просмотр списка созданных резервных копий с информацией о размере и дате.
    * Скачивание резервных копий через админ-панель.
    * Удаление резервных копий через админ-панель.
* **Восстановление из резервной копии**:
    * Функциональность восстановления через админ-панель (для рабочего сервера).
    * **Специальный скрипт `restore.php`** для восстановления на новом или "пустом" сервере (вне админ-панели).

---

## Требования

### Основные:

* **Cockpit CMS**: v2.x (модуль разрабатывался для этой версии).
* **PHP**: v8.1+ (или выше, в зависимости от требований Cockpit CMS).

### Для восстановления через скрипт `restore.php`:

* **Расширение PHP `Phar`**: Должно быть включено в конфигурации PHP.
* **Для резервного копирования/восстановления MongoDB**:
    * Функция `shell_exec` должна быть включена в конфигурации PHP.
    * Утилиты `mongodump` и `mongorestore` должны быть установлены на сервере и доступны через PATH.

---

## Установка

1. Скопируйте папку `Backup` из этого репозитория в директорию `your_cockpit_root/addons/`.
   При необходимости скопируйте файл с переводами в директорию `your_cockpit_root/config/`.
   Ваша структура папок должна выглядеть примерно так:
   ```
   your_project_root/
   ├── cockpit/
   │   └── addons/
   │   │   └── Backup/
   │   │       ├── Controller/
   │   │       │   └── ...
   │   │       ├── Helper/
   │   │       │   └── ...
   │   │       ├── views/
   │   │       │   └── ...
   │   │       ├── admin.php
   │   │       ├── bootstrap.php
   │   │       ├── icon.svg
   │   │       └── restore.php
   │   └── config/
   │       └── i18n/
   │           ├── ru/
   │           │   └── Backup.php
   ```
2. Войдите в админ-панель Cockpit.
3. Перейдите в `Настройки` -> `Резервное копирование`.

---

## Конфигурация

Конфигурация модуля осуществляется через админ-панель Cockpit:

1. В админ-панели перейдите в `Настройки` -> `Резервное копирование`.
2. На вкладке **"Настройки резервного копирования"**:
    * **Конфигурация резервного копирования**: Выберите, какие части системы будут включены в резервную копию (Ядро
      Cockpit, Файлы сайта, База данных).
    * **Исключенные пути**: Укажите пути (относительно корня сайта) для исключения из резервной копии. Например:
      `my_temp_folder`, `cockpit/cache/some_large_files`.
      **Дополнительная настройка пути для сохранения резервных копий (через файл `cockpit/config/config.php`):**
      По умолчанию резервные копии сохраняются в `your_cockpit_root/backups`. Вы можете изменить это, добавив следующую
      секцию
      в `cockpit/config/config.php`:

```php
<?php
return [
    'backup' => [
        'config' => [
            'backup_path' => '/path_to_your_custom_backups_folder',
        ],
    ],
];
```

---

## Использование

### Создание и управление резервными копиями

1. Перейдите в `Настройки` -> `Резервное копирование`.
2. На вкладке **"Список резервных копий"**:
    * Вы увидите список всех ранее созданных резервных копий.
    * Для каждой резервной копии доступны действия:
        * **Скачать** (`download`): Скачивает архив резервной копии на ваш компьютер.
        * **Восстановить** (`restore`): Запускает процесс восстановления с текущего сервера. **ВНИМАНИЕ:** Это
          перезапишет существующие файлы и базу данных.
        * **Удалить** (`delete`): Удаляет файл резервной копии.
    * Кнопка **"Создать резервную копию"** запускает процесс создания новой резервной копии в соответствии с текущими
      настройками.
    * Кнопка **"Обновить список резервных копий"** обновляет список файлов резервных копий.

### Восстановление с помощью скрипта `restore.php`)

Этот метод предназначен для случаев, когда Cockpit CMS не функционирует или устанавливается на новом сервере.
**КРИТИЧЕСКИ ВАЖНО:**

1. **Размещение скрипта `restore.php`**:
    * Скачайте файл `restore.php` из админ-панели (раздел "Список резервных копий", ссылка "Скачать файл для
      восстановления из резервной копии: restore.php").
    * **Загрузите этот `restore.php` в КОРНЕВУЮ ДИРЕКТОРИЮ ВАШЕГО САЙТА**.
    * Например, если ваш домен `example.com` указывает на `public_html/`, то `restore.php` должен быть в `public_html/`.
      Папка `cockpit` после восстановления будет в `public_html/cockpit`.
2. **Размещение файла резервной копии**:
    * Загрузите ваш `.tar.gz` файл резервной копии в **ту же директорию**, что и скрипт `restore.php`.
3. **Запуск процесса восстановления**:
    * Откройте `restore.php` в браузере (например, `https://yourdomain.ru/restore.php`).
    * Скрипт автоматически обнаружит файлы резервных копий в текущей директории и выберет самый свежий.
    * На странице подтверждения восстановления:
        * Проверьте детали резервной копии.
        * **Убедитесь, что пути к установке Cockpit CMS и папке данных Mongolite указаны корректно.** Скрипт попытается
          предложить пути по умолчанию на основе своего расположения, но они могут потребовать корректировки.
        * Нажмите **"Начать восстановление"**.
    * Скрипт выполнит следующие действия:
        * Извлечет архив во временную директорию.
        * Удалит существующие файлы в целевых директориях (Cockpit, файлы проекта) перед копированием из резервной
          копии.
        * Скопирует файлы ядра Cockpit и файлы проекта.
        * **Установит необходимые права доступа** для всех восстановленных файлов и директорий.
        * Восстановит базу данных (Mongolite копированием файлов, MongoDB через `mongorestore`).
        * Очистит кэш Cockpit CMS.
        * Удалит временные файлы.
    * После успешного восстановления на странице появится:
        * Ссылка **"Начать сначала"**.
        * Кнопка **"Удалить файлы восстановления и перейти на сайт"**.

**КРАЙНЕ ВАЖНОЕ ПРЕДУПРЕЖДЕНИЕ:**

* **После успешного восстановления ВЫ ДОЛЖНЫ НЕМЕДЛЕННО УДАЛИТЬ файл `restore.php` с сервера.** Это мощный инструмент,
  который может быть использован злоумышленниками для полного перезаписывания вашего сайта. Используйте кнопку "Удалить
  файл резервной копии и скрипт восстановления" для безопасной очистки. Если автоматическое удаление не удалось, удалите
  файлы вручную.

---

## Лицензия

Модуль распространяется под лицензией [GNU Affero General Public License v3 (AGPLv3)](https://www.gnu.org/licenses/agpl-3.0.html).

---

## Контакты

Автор: Yuriy Plotnikov  
Сайт: https://yuriyplotnikovv.ru
