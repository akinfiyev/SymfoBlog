<?php
/**
 * Created by PhpStorm.
 * User: q
 * Date: 20.11.18
 * Time: 15:07
 */

namespace App\Services;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UserService
{
    private $encoder;

    /**
     * UserService constructor.
     */
    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function encodePassword(User $user)
    {
        $plainPassword = $user->getPlainPassword();

        if (!empty($plainPassword)) {
            return $this->encoder->encodePassword($user, $plainPassword);
        }
        return $plainPassword;
    }
}
