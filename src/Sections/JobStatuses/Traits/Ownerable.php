<?php

namespace NetLinker\FairQueue\Sections\JobStatuses\Traits;

use Illuminate\Support\Facades\Auth;
use NetLinker\FairQueue\Tests\Stubs\User;

trait Ownerable
{

    /** @var string $authUuid */
    protected $authUuid;

    /**
     * Get auth user job
     *
     * @return User
     */
    private function getAuthUserJob()
    {
        $fieldUuid = config('fair-queue.owner.field_auth_user_owner_uuid');
        $userClass = config('auth.providers.users.model');

        return $userClass::where($fieldUuid, $this->authUuid)->firstOrFail();
    }

    /**
     * Login user job
     */
    private function loginUserJob()
    {
        $user = $this->getAuthUserJob();
        Auth::login($user);
    }

    /**
     * Get owner model name.
     *
     * @return string
     */
    protected function getOwnerModel()
    {
        return config('fair-queue.owner.model');
    }

    /**
     * Prepare auth user job
     *
     * @param string|null $authUuid
     */
    protected function prepareAuthUserJob($authUuid = null){
        if (!$authUuid){
            $this->authUuid = $this->getAuthUserUuid();
        } else {
            $this->authUuid = $authUuid;
        }
    }

    /**
     * Get auth user Uuid
     *
     * @return null|\Cog\Contracts\Ownership\CanBeOwner
     */
    public function getAuthUserUuid()
    {
        $fieldUuid = config('fair-queue.owner.field_auth_user_owner_uuid');
        return Auth::user()->$fieldUuid;
    }

    /**
     * Get auth owner ID
     *
     * @return null|\Cog\Contracts\Ownership\CanBeOwner
     */
    public function getAuthOwnerId()
    {
        $fieldUuid = config('fair-queue.owner.field_auth_user_owner_uuid');
        $model = $this->getOwnerModel();
        $owner = $model::where('uuid', Auth::user()->$fieldUuid)->firstOrFail();
        return $owner->id;
    }
}
