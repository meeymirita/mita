<?php

namespace App\Services\User;

use App\Contracts\User\UpdateUserInterface;
use App\Models\User;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\UploadedFile;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileDoesNotExist;
use Spatie\MediaLibrary\MediaCollections\Exceptions\FileIsTooBig;

class UpdateService implements UpdateUserInterface
{
    public function update(array $userData): User|JsonResponse|Authenticatable|null
    {
        $user = auth()->user();
        if (isset($userData['avatars'])) {
            $this->handleAvatarsUpload($user, $userData['avatars']);
            unset($userData['avatars']);
        }
        try {
            if (!empty($userData)) {
                $user->update($userData);
            }
            return $user->fresh();
        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Ошибка при обновлении профиля',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @param User $user
     * @param object $avatarsFile
     * @return object
     */
    public function handleAvatarsUpload(User $user, object $avatarsFile): object
    {
        try {
            if($avatarsFile instanceof UploadedFile){
                $user->clearMediaCollection('avatars');
                // https://spatie.be/docs/laravel-medialibrary/v11/basic-usage/associating-files
                return $user->addMedia($avatarsFile)->toMediaCollection('avatars');
            }
        } catch (FileDoesNotExist|FileIsTooBig $e) {
            return response()->json([
                'message' => $e->getMessage(),
            ]);
        }
    }
}
