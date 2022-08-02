<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use App\Validator as CustomAssert;
use DateTime;
use Webmozart\Assert\Assert as AssertAssert;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank()]
    #[Assert\Email()]
    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;
    
    #[CustomAssert\Pesel()]
    // #[Assert\Unique()]
    #[ORM\Column(length: 11)]
    private ?string $pesel = null;

    #[Assert\NotBlank()]
    #[ORM\Column(length: 255)]
    private ?string $firstName = null;

    #[Assert\NotBlank()]
    #[ORM\Column(length: 255)]
    private ?string $lastName = null;

    #[ORM\Column(type: Types::DATE_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $birthDate = null;

    #[CustomAssert\UniqueName()]
    #[ORM\OneToMany(mappedBy: 'user', targetEntity: ProgrammingLanguage::class, cascade:["persist"])]
    private Collection $programmingLanguages;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $fromWhere = null;

    #[ORM\Column]
    private ?bool $is_active = false;

    #[ORM\Column(type: Types::DATETIME_MUTABLE, nullable: true)]
    private ?\DateTimeInterface $created = null;

    public function __construct()
    {
        $this->programmingLanguages = new ArrayCollection();
    }

    public function __toString()
    {
        $this->firstName.' '.$this->lastName;
    }

    public function getId(): ?int
    {
        return $this->id;
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

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
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
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    public function getPesel(): ?string
    {
        return $this->pesel;
    }

    public function setPesel(string $pesel): self
    {
        $this->pesel = $pesel;

        return $this;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getBirthDate(): ?\DateTimeInterface
    {
        return $this->birthDate;
    }

    public function setBirthDate(?\DateTimeInterface $birthDate): self
    {
        $this->birthDate = $birthDate;

        return $this;
    }

    /**
     * @return Collection<int, ProgrammingLanguage>
     */
    public function getProgrammingLanguages(): Collection
    {
        return $this->programmingLanguages;
    }

    public function addProgrammingLanguage(ProgrammingLanguage $programmingLanguage): self
    {
        if (!$this->programmingLanguages->contains($programmingLanguage)) {
            $this->programmingLanguages->add($programmingLanguage);
            $programmingLanguage->setUser($this);
        }

        return $this;
    }

    public function removeProgrammingLanguage(ProgrammingLanguage $programmingLanguage): self
    {
        if ($this->programmingLanguages->removeElement($programmingLanguage)) {
            // set the owning side to null (unless already changed)
            if ($programmingLanguage->getUser() === $this) {
                $programmingLanguage->setUser(null);
            }
        }

        return $this;
    }

    public function getFromWhere(): ?string
    {
        return $this->fromWhere;
    }

    public function setFromWhere(?string $fromWhere): self
    {
        $this->fromWhere = $fromWhere;

        return $this;
    }

    public function isIsActive(): ?bool
    {
        return $this->is_active;
    }

    public function setIsActive(bool $is_active): self
    {
        $this->is_active = $is_active;

        return $this;
    }

    public function getCreated(): ?\DateTimeInterface
    {
        return $this->created;
    }
    
    public function setCreated(\DateTimeInterface $created): self
    {
        $this->created = $created;

        return $this;
    }

    #[ORM\PrePersist]
    public function onPrePersist()
    {
        $this->created = new DateTime('now');
    }

    public function getPeriodToAdult(): ?string
    {
        $now = new DateTime('now');
        
        if ($this->birthDate->diff($now)->y < 18) {
            $adultAge = $this->birthDate->modify('+18 years');
            $yearsTotal = $now->diff($adultAge)->y;
            $daysExludingYears = ($now->diff($adultAge->modify('-'.$yearsTotal.' years'))->days);
            return $yearsTotal.' lat '.$daysExludingYears. 'dni';
        } else {
            return 'TAK';
        }
    }

}
