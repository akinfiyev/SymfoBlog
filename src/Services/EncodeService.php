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

class EncodeService
{
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
    }

    public function encodeUserPassword(?String $plainPassword, User $user)
    {
        if (!empty($plainPassword)) {
            return $this->encoder->encodePassword($user, $plainPassword);
        }

        return $plainPassword;
    }
}
