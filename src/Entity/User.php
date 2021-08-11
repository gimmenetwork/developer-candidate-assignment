<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @ORM\Table(name="`user`")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank()
     * @Assert\Email()
     */
    private $email;

    /**
    * @ORM\Column(type="json")
    */
   private $roles = [];
    
    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     */
    private $plainPassword;

    /**
     * @ORM\OneToMany(targetEntity=LeaseHistory::class, mappedBy="lessee")
     */
    private $leaseHistories;

    /**
     * @ORM\Column(type="integer")
     */
    private $bookLimit;

    public function __construct()
    {
        $this->leaseHistories = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }


    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        if( ! $password ) {
            $password = $this->password;
        }
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }
    public function setPlainPassword(string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * @return Collection|LeaseHistory[]
     */
    public function getLeaseHistories(): Collection
    {
        return $this->leaseHistories;
    }

    public function addLeaseHistory(LeaseHistory $leaseHistory): self
    {
        if (!$this->leaseHistories->contains($leaseHistory)) {
            $this->leaseHistories[] = $leaseHistory;
            $leaseHistory->setLessee($this);
        }

        return $this;
    }

    public function removeLeaseHistory(LeaseHistory $leaseHistory): self
    {
        if ($this->leaseHistories->removeElement($leaseHistory)) {
            // set the owning side to null (unless already changed)
            if ($leaseHistory->getLessee() === $this) {
                $leaseHistory->setLessee(null);
            }
        }

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getBookLimit(): ?int
    {
        return $this->bookLimit;
    }

    public function setBookLimit(int $bookLimit): self
    {
        $this->bookLimit = $bookLimit;

        return $this;
    }


    public function getRemainingLimit()
    {
        $leased = 0;

        foreach ($this->leaseHistories as $lease) {
            
            if(!$lease->getReturned())
            {
                $leased= $leased+ 1;
            }
            
        }

        return $this->bookLimit- $leased;
    }


}
