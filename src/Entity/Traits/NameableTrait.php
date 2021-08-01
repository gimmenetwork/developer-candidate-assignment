<?php

namespace App\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

trait NameableTrait
{
    /**
     * @Assert\NotBlank()
     * @ORM\Column(type="string", length=255)
     */
    private ?string $name;

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     */
    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }
}
