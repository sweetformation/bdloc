<?php

namespace Bdloc\AppBundle\Security;

use Symfony\Component\Security\Core\Validator\Constraints as SecurityAssert;
use Symfony\Component\Validator\Constraints as Assert;


class ChangePassword
{

    /**
     * @SecurityAssert\UserPassword(
     *     message = "Votre mot de passe actuel est erroné"
     * )
     */
    private $oldPassword;

    /**
     * @Assert\Length(
     *     min = 6,
     *     minMessage = "Le mot de passe doit contenir au moins {{ limit }} caractères."
     * )
     */
    protected $newPassword;

}