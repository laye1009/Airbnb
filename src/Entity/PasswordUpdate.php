<?php

namespace App\Entity;
use Symfony\Component\Validator\Constraints as Assert;

use App\Repository\PasswordUpdateRepository;


class PasswordUpdate
{
   
    private $oldPassword;
    /**
     * @Assert\Length(min=8,minMessage="Votre mot de passe doit faire au moins 8 caractère")
     */

  
    private $newPassword;
    /**
     * @Assert\EqualTo(propertyPath="newPassword",message="Vous n'avez pas correctement confirmé votre mot de passe")
     */

   
    private $confirmPassword;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOldPassword(): ?string
    {
        return $this->oldPassword;
    }

    public function setOldPassword(string $oldPassword): self
    {
        $this->oldPassword = $oldPassword;

        return $this;
    }

    public function getNewPassword(): ?string
    {
        return $this->newPassword;
    }

    public function setNewPassword(string $newPassword): self
    {
        $this->newPassword = $newPassword;

        return $this;
    }

    public function getConfirmPassword(): ?string
    {
        return $this->confirmPassword;
    }

    public function setConfirmPassword(string $confirmPassword): self
    {
        $this->confirmPassword = $confirmPassword;

        return $this;
    }
}
