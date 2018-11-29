<?php
/**
 * Created by PhpStorm.
 * User: q
 * Date: 29.11.18
 * Time: 14:27
 */

namespace App\EntityListener;

use App\Entity\User;
use App\Services\UserService;
use Doctrine\ORM\Event\LifecycleEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Mapping\PrePersist;
use Doctrine\ORM\Mapping\PreUpdate;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserListener
{
    /**
     * @var UserService
     */
    private $userService;

    /**
     * UserListener constructor.
     * @param $userService
     */
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /** @PrePersist */
    public function prePersistHandler(User $user, LifecycleEventArgs $event)
    {
        $user->setPassword($this->userService->encodePassword($user));
    }
}
