<?php

namespace App\Entity;


use Doctrine\ORM\Mapping as ORM;
use App\Repository\RoleRepository;

/**
 * @ORM\Entity(repositoryClass=RoleRepository::class)
 */
class Role
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $name;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $role;

    /**
     * @ORM\Column(type="json", nullable=true)
     */
    private $accessList = [];

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getRole(): ?string
    {
        return $this->role;
    }

    public function setRole(string $role): self
    {
        $this->role = $role;

        return $this;
    }

    public function getAccessList(): array
    {
        $accessList = $this->accessList;
        // guarantee every user at least has dashboard
        $accessList[] = 'dashboard';

        return array_unique($accessList);
    }

    public function setAccessList(array $accessList): self
    {
        $this->accessList = $accessList;

        return $this;
    }
}