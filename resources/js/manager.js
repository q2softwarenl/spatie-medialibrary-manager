document.addEventListener('alpine:init', () => {

    Alpine.data('spatie_medialibrary_manager', (livewireComponent) => ({
        
        isEditing: false,
        isMoving: false,
        isUploading: false,
        isDeleting: false,
        progress: 0,
        activeCollectionLabel: '',

        /**
         * Entangled variables
         */
        managerKey: livewireComponent.entangle('managerKey'), 

        maxFileSizeBytes: livewireComponent.entangle('maxFileSizeBytes'),
        
        canOverview: livewireComponent.entangle('canOverview'),
        
        canDownload: livewireComponent.entangle('canDownload'),
        canEdit: livewireComponent.entangle('canEdit'),
        canMove: livewireComponent.entangle('canMove'),

        allMediaCollections: livewireComponent.entangle('allMediaCollections'),
        allMediaItems: livewireComponent.entangle('allMediaItems'),

        totalFilesCount: livewireComponent.entangle('totalFilesCount'),
        totalFileSizeMb: livewireComponent.entangle('totalFileSizeMb'),
        totalFileSize: livewireComponent.entangle('totalFileSize'),
        activeCollection: livewireComponent.entangle('activeCollection'),

        /**
         * Init
         */
        init() {},

        /**
         * Navigation
         */
        async navigateToCollection(collection) {
            this.activeCollectionLabel = collection.label
            livewireComponent.activeCollection = collection.collection_name
            this.activeCollection = collection.collection_name

            if(this.allMediaItems.filter(item => item.thumbnail_url === '').length > 0)
                livewireComponent.call('tryLoadingMissingThumbnails')
        },

        async navigateToHome() {
            if(!this.canOverview) return;

            this.activeCollectionLabel = ''
            livewireComponent.activeCollection = this.activeCollection = null
            
            this.allMediaItems.map(media => {
                if(media.editing)
                    this.cancelEditing(media)

                if(media.moving)
                    this.cancelMoving(media)
            })
        },

        /**
         * Upload new mediaItem(s)
         */
        async handleFileUpload(e, collection) {
            if (e.target.files.length == 0) return

            this.processFileUpload(e.target.files, collection)
        },

        async handleFileDrop(e, collection) {
            if (e.dataTransfer.files.length == 0) return

            this.processFileUpload(e.dataTransfer.files, collection)
        },

        processFileUpload(files, collection) {
            if(!collection.canUpload) {
                console.error('Uploading to this collection is not allowed.') 
                return
            }
            
            this.isUploading = true

            livewireComponent.uploadingToMediaCollection = collection.collection_name

            const rawFiles = [...files].filter(file => file.size <= this.maxFileSizeBytes)

            if (rawFiles.length == 0) {
                this.isUploading = false
                this.progress = 0

                throw new Error('Filesize exceeds upload limit.')
            }

            if(rawFiles.length > 20) {
                this.isUploading = false
                this.progress = 0

                throw new Error('You can only upload 20 files at the same time.')
            }

            // if (e.target.files[0].size > this.maxFileSizeBytes) throw new Error('Filesize exceeds upload limit.')

            livewireComponent.uploadMultiple(
                'rawFiles', 
                rawFiles, 
                (filename) => { // Success
                    this.isUploading = false
                    this.progress = 0

                    setTimeout(function() {
                        livewireComponent.call('tryLoadingMissingThumbnails')
                    }, 5000)
                }, 
                (error) => { // Error
                    this.isUploading = false
                    this.progress = 0
                }, 
                (event) => {
                    this.progress = event.detail.progress
                }
            )
        },

        /**
         * Update mediaItem filename
         */
        async editMediaItem(media, collection) {
            if(this.isEditing || !collection.canEdit) return;

            media.updatingName = media.name
            media.editing = this.isEditing = true
        },

        async updateMediaItem(media, collection) {
            if(!collection.canEdit) return;

            media.editing = this.isEditing = false
            media.name = media.updatingName

            livewireComponent.updateMediaItemName(media.id, media.name)
        },

        async cancelEditing(media) {
            media.editing = this.isEditing = false
            media.updatingName = media.name
        },

        /**
         * Download mediaItem(s)
         */
        async downloadMediaItem(media, collection) {
            if(!this.canDownload || !collection.canDownload) return;

            livewireComponent.downloadMediaItem(media.id)
        },

        async downloadAll(collection) {
            livewireComponent.call('downloadAll', collection.collection_name)
        },

        /**
         * Move a mediaItem between collections
         */
        async movingMediaItem(media, collection) {
            if(this.isMoving || !this.canMove || !collection.canMove) return;

            const collectionNames = Reflect.ownKeys(this.allMediaCollections)

            media.moveToMediaCollectionOptions = collectionNames.filter(collection_name => collection_name !== media.collection_name).filter(collection_name => {
                const dest_collection = Reflect.get(this.allMediaCollections, collection_name)
                
                return !dest_collection.singleFile
            }).map(collection_name => {
                const collection = Reflect.get(this.allMediaCollections, collection_name)
                
                return { 
                    'value': collection_name,
                    'label': collection.label
                }
            })
            
            media.moving = this.isMoving = true && media.moveToMediaCollectionOptions.length > 0
        },

        async moveMediaItem(media, collection) {
            if(!this.canMove || !collection.canMove) return;

            media.moving = this.isMoving = false
            media.collection_name = media.moveToMediaCollectionName

            livewireComponent.moveMediaItem(media.id, media.collection_name)
        },

        async cancelMoving(media) {
            media.moving = this.isMoving = false
            media.moveToMediaCollectionOptions = media.moveToMediaCollectionName = undefined
        },

        /**
         * Delete a mediaItem
         */
        async deleteMediaItem(managerKey, media, collection, confirmationMessage = 'Are you sure to delete this file? You cannot undo this action.') {
            if(this.isDeleting || !collection.canDelete) return;

            media.deleting = this.isDeleting = true

            if(confirm(confirmationMessage)) {                
                livewireComponent.call('deleteMediaItem', managerKey, media.id)
            }
                
            this.isDeleting = false
        },

    }))
})