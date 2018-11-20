<?php
/**
 * Created by PhpStorm.
 * User: q
 * Date: 20.11.18
 * Time: 14:46
 */

namespace App\EventListener;


use Symfony\Component\EventDispatcher\Event;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class PasswordListener
{
    private $passwordEncoder;

    /**
     * PasswordListener constructor.
     */
    public function __construct(UserPasswordEncoderInterface $passwordEncoder)
    {
        $this->passwordEncoder = $passwordEncoder;
    }

    public function onPasswordEnteringEvent(Event $event)
    {
        $user = $event->getUser();
        $plainPassword = $event->getUser()->getPassword();
        $encoded = $this->passwordEncoder->encodePassword($user, $plainPassword);
        $event->getUser()->setPassword($encoded);
    }
}