<?php

namespace App\EntityListener;

use App\Entity\User;
use App\Services\EncodeService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Mapping\PrePersist;

class UserListener
{
    /**
     * @var EncodeService
     */
    private $userService;

    /**
     * UserListener constructor.
     * @param $userService
     */
    public function __construct(EncodeService $userService)
    {
        $this->userService = $userService;
    }

    /** @PrePersist */
    public function prePersistHandler(User $user)
    {
        $user->setPassword($this->userService->encodeUserPassword($user->getPlainPassword(), $user));
    }
}
