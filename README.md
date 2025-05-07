# Spatie Medialibrary Manager
A file manager for Laravel applications using the famous Spatie [Laravel Medialibrary package](https://github.com/spatie/laravel-medialibrary).

**Features**
- Manage multiple media collections via one interactive GUI
- Manage singlefile and multifile collections
- Download, rename or delete media
- Move media between collections
- Manage policies via native Laravel Policies or set policies directly on the component.

![Manager](./manager.png)

**PRO-features**
- Set tailored policies on the collection, rather than being limited to just the model.
- Add custom actions to files.
- Upload multiple versions of the same file.
- Alternative GUI for single file collections.

**Roadmap**

**Note!** We are currently in active development of "Spatie Medialibrary Manager Pro". We plan to publish a free version here on Github. There are a few things that need to be fixed before we can publish the package.

- **alpha** Bug: Decide if thumbs are a requirement  
- **alpha** Refactor: Use json translation as much as possible and avoid legacy php arrays  
- **beta** Feature: Filesize validation
- **beta** Feature: Policies to set rules for individual collections
- **beta** Feature: Add active folder to url.
- **beta** Improve single file collections.
- **v1.1** Add support for darkmode.

The alpha version is released at the end of April. 
The beta version is scheduled for release at the end of May.

## Requirements
- Laravel 10+
- Tailwindcss 4
- Spatie Medialibrary 11+

Please note that this package requires a base installation of Laravel Media Library. Do this first if you haven't done so already. [How to setup Laravel Media Library?](https://spatie.be/docs/laravel-medialibrary/v11/installation-setup)

## Base installation

We assume that you have installed and configured Spatie and that the Laravel Models are prepared. If not, please read that documentation first.
- [How to install Spatie Laravel Medialibrary?](https://spatie.be/docs/laravel-medialibrary/v11/installation-setup)
- [How to prepare Models?](https://spatie.be/docs/laravel-medialibrary/v11/basic-usage/preparing-your-model)

Spatie Medialibrary Manager can be installed via Composer. To do so, follow the basic installation instructions below. 

```bash
composer require q2softwarenl/spatie-medialibrary-manager
```

1. Add the manager javascript to your app `resources/js/app.js` file:
    ```diff
    import './bootstrap';
    + import './../../vendor/q2softwarenl/spatie-medialibrary-manager/resources/js/manager';
    ```

2. Import the manager styles to your app `app.css` file:
    ```css
    @import './../../vendor/q2softwarenl/spatie-medialibrary-manager/resources/css/manager.css'; 
    @import './../../vendor/q2softwarenl/spatie-medialibrary-manager/resources/css/theme/default.css';
    ```

3. Run `npm run build`.

4. Configure the model policy before you can use the manager. After that, you can configure the manager with custom language files, custom configuration and display. Use the following policies for each model that has media and implenents the Spatie Medialibrary Manager:

    - `spatieMedialibraryManagerEditMedia`
    - `spatieMedialibraryManagerDeleteMedia`
    - `spatieMedialibraryManagerMoveMedia`
    - `spatieMedialibraryManagerUploadMedia`
    - `spatieMedialibraryManagerDownloadMedia`
    - `spatieMedialibraryManagerDownloadAllMedia`
    - [Take a look at the UserPolicy.php sample file](./examples/UserPolicy.php)
    - [Policies can be overruled to be `false`](#overrule-policies)

5. Add the component to a view. The manager will auto-detect registered mediacollections after you have prepared your models. [How to register a media collection?](#register-a-media-collection)

    ```html
    <livewire:spatie-medialibrary-manager
        :model="$user"
    />
    ```

6. **Only required for PRO users**: Follow the steps in the PRO documentation "Preparing Laravel Models"-section.

## Register a media collection
Create a function called `registerMediaCollections` in the model where you want to use media. In this example we are using the `User` model. 

Example:

```php
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\HasMedia;

class User extends Authenticatable implements HasMedia { 
    
    use InteractsWithMedia;
    
    public function registerMediaCollections() : void
    {
        $this->addMediaCollection('avatar')->singleFile();
        $this->addMediaCollection('images');
    }

}
```

## Language
Spatie Medialibrary Manager uses default translation strings. You can edit this in you applications `lang\<your-locale>.json` file. You can use this Dutch template example:

```json
{
    "Back": "Terug",
	"Cancel": "Annuleren",
	"Caution!": "Let op!",
	"Choose a file": "Kies een bestand",
	"Choose files": "Kies bestanden",
	"Choose file": "Kies bestand",
	"Delete file": "Verwijder bestand",
	"Download All": "Alles downloaden",
	"Download": "Downloaden",
	"Move file": "Verplaats bestand",
	"Original filename": "Originele bestandsnaam",
	"Rename file": "Hernoem bestand",
	"Save": "Opslaan",
	"This folder only accepts one file.": "Deze map accepteert maar één bestand.",
	"Where would you like to move \":file_name\"?": "Waarheen wilt u \":file_name\" verplaatsen?"
}
```

Publish the language file to set other translations:

```bash
vendor:publish --provider="Q2softwarenl\SpatieMedialibraryManager\SpatieMedialibraryManagerServiceProvider" --tag="lang"
```

## Policies

[Take a look at the UserPolicy.php sample file](./examples/UserPolicy.php).

### Overrule policies

Sometimes, you want the MediaManager to be readonly or download only, etc., even if the user is according to policies able to do write actions. You can overrule policies that return `true`:

- `canUpload` (default: true, fallback to policy) (if set to `false`, this prevents all upload actions)
- `canDownload` (default: true, fallback to policy) (if set to `false`, this prevents all download actions)
- `canEdit` (default: true, fallback to policy) (if set to `false`, this prevents all rename actions)
- `canMove` (default: true, fallback to policy) (if set to `false`, this prevents all move actions)
- `canDelete` (default: true, fallback to policy) (if set to `false`, this prevents all delete actions)

Example:

```html
<livewire:spatie-medialibrary-manager
    ...
    :canUpload="false"
    :canDownload="false"
    :canEdit="false"
    :canMove="false"
    :canDelete="false"
/>
```

**Note!** You cannot force `true` if the policy returns `false`. Make sure your policy covers the basics and manage functional policies via the component attributes.