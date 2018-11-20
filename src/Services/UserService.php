<?php
/**
 * Created by PhpStorm.
 * User: q
 * Date: 20.11.18
 * Time: 15:07
 */

namespace App\Services;


use App\Entity\User;
use App\Event\PasswordEnteringEvent;
use App\EventListener\PasswordListener;
use Doctrine\Common\Persistence\ManagerRegistry;
use Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class UserService
{
    private $dispatcher;
    private $doctrine;
    private $listener;

    /**
     * UserService constructor.
     */
    public function __construct(EventDispatcherInterface $dispatcher, ManagerRegistry $doctrine, PasswordListener $listener)
    {
        $this->dispatcher = $dispatcher;
        $this->doctrine = $doctrine;
        $this->listener = $listener;
    }

    public function save(User $user) {
        $event = new PasswordEnteringEvent($user);
        $this->dispatcher
            ->addListener(
                PasswordEnteringEvent::NAME,
                [$this->listener, 'onPasswordEnteringEvent']);

        $encodePassword = $this->dispatcher->dispatch(PasswordEnteringEvent::NAME, $event)->getUser()->getPassword();
        $user->setPassword($encodePassword);

        $this->doctrine->getManager()->persist($user);
        $this->doctrine->getManager()->flush();
        return $this;
    }
}