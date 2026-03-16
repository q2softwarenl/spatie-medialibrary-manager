# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

A Laravel package providing an interactive GUI file manager for [Spatie Laravel Medialibrary](https://spatie.be/docs/laravel-medialibrary). Built with Livewire 3, Alpine.js, and Tailwind CSS 4. Allows managing multiple media collections with upload, download, rename, move, and delete operations plus policy-based authorization.

## Commands

```bash
# Run tests
composer test

# Start development server (builds workbench + serves)
composer serve

# Build workbench only
composer build

# Clear/reset workbench skeleton
composer clear

# Discover packages
composer prepare

# Run a single test file
php vendor/bin/phpunit tests/Feature/ManagerTest.php

# Run a single test method
php vendor/bin/phpunit --filter testMethodName
```

## Architecture

### Core Components

**[src/Livewire/Manager.php](src/Livewire/Manager.php)** — The main Livewire component. Handles all server-side logic: collection loading, file operations (upload/rename/move/delete/download), policy authorization, and reactive state. Uses `WithFileUploads` trait and Livewire 3 attributes (`#[Locked]`, `#[Url]`, `#[Validate]`, `#[On]`).

**[src/ManagerFile.php](src/ManagerFile.php)** — Value object wrapping a Spatie `Media` model. Provides file type detection from MIME types (image, pdf, excel, word, zip, unknown), thumbnail URL generation, human-readable size, and factory methods: `fromModel()`, `fromJs()`, `toMediaCollection()`.

**[resources/js/manager.js](resources/js/manager.js)** — Alpine.js component (`spatie_medialibrary_manager`). Manages client-side UI state: drag-and-drop uploads, inline editing, move modal, file operation queues. Entangles reactive properties with Livewire.

### Data Flow

1. Host app embeds `<livewire:spatie-medialibrary-manager :model="$model" />`
2. `Manager.php` loads the model's media collections (must implement `HasMedia` + `InteractsWithMedia`)
3. Server state is reflected in the Blade template with Alpine.js managing local UI interactions
4. User actions dispatch Livewire events or call Livewire methods directly
5. Livewire performs the operation, checks policy, updates `$files` array, re-renders

### Authorization

The component checks six Laravel policy methods on the model:
- `spatieMedialibraryManagerEditMedia`
- `spatieMedialibraryManagerDeleteMedia`
- `spatieMedialibraryManagerMoveMedia`
- `spatieMedialibraryManagerUploadMedia`
- `spatieMedialibraryManagerDownloadMedia`
- `spatieMedialibraryManagerDownloadAllMedia`

These can also be overridden via boolean component props: `canUpload`, `canDownload`, `canEdit`, `canMove`, `canDelete`.

### Testing

Uses [Orchestra Testbench](https://packages.tools/testbench) with a workbench app in [workbench/](workbench/). The workbench provides a real Laravel app context with a `User` model, migrations, factories, and routes for integration testing.

- [tests/TestCase.php](tests/TestCase.php) — Base class configuring Testbench with the package service provider
- [tests/Unit/ManagerFileObjectTest.php](tests/Unit/ManagerFileObjectTest.php) — Tests for `ManagerFile` value object
- [tests/Feature/ManagerTest.php](tests/Feature/ManagerTest.php) — Integration tests for the Livewire component

### Frontend Assets

CSS and JS must be imported directly from the vendor path by the host application — no build step is required for the package itself:
- `resources/js/manager.js` — Alpine.js component
- `resources/css/manager.css` — Component styles (imports theme files)
- `resources/css/theme/default.css` and `daisyui5.css` — Theme variants
