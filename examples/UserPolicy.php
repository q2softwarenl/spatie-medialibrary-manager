<?php

use App\Models\User;
use App\Models\Invoice;

class UserPolicy {

    public function spatieMedialibraryManagerEditMedia(User $user, Invoice $invoice): bool
    {
        return true;
    }

    public function spatieMedialibraryManagerDeleteMedia(User $user, Invoice $invoice): bool
    {
        return true;
    }

    public function spatieMedialibraryManagerMoveMedia(User $user, Invoice $invoice): bool
    {
        return true;
    }

    public function spatieMedialibraryManagerDownloadMedia(User $user, Invoice $invoice): bool
    {
        return true;
    }

    public function spatieMedialibraryManagerUploadMedia(User $user, Invoice $invoice): bool
    {
        return true;
    }

    public function spatieMedialibraryManagerDownloadAllMedia(User $user, Invoice $invoice): bool
    {
        return true;
    }


}