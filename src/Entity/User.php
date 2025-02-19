<?php

namespace App\Entity;

use App\Repository\RequestComicLoanRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\HttpFoundation\File\File;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Vich\UploaderBundle\Mapping\Annotation as Vich;

/**
 * @ORM\Entity(repositoryClass="App\Repository\UserRepository")
 * @Vich\Uploadable()
 * @UniqueEntity(fields={"pseudoname"}, message="There is already an account with this pseudoname")
 */
class User implements UserInterface
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue()
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @Assert\NotBlank(message="Fill a pseudo")
     * @Assert\Length(max="255",maxMessage="Le pseudo {{ value }} have too much characters, {{ limit }} characters maximum")
     */
    private $pseudoname;

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
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $firstName;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $lastName;

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    private $age;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Fill a city")
     */
    private $city;

    /**
     * @Vich\UploadableField(mapping="user_image", fileNameProperty="avatarName")
     *
     * @var File
     */
    private $avatarPicture;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     *
     * @var string
     */
    private $avatarName;

    /**
     * @ORM\Column(type="datetime")
     */
    private $dateCreated;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Select a country")
     */
    private $country;

    /**
     * @ORM\Column(type="string", length=255)
     * @Assert\NotBlank(message="Fill an email")
     */
    private $email;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserLibrary", mappedBy="user", orphanRemoval=true)
     */
    private $userLibraries;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\ComicLoan", mappedBy="UserLoaner")
     */
    private $comicLoans;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\RequestComicLoan", mappedBy="user")
     */
    private $requestComicLoans;

    /**
     * @ORM\Column(type="datetime", nullable=true)
     *
     * @var \DateTime
     */
    private $updatedAt;
    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserRate", mappedBy="user")
     * @ORM\OrderBy({"dateAt" = "DESC"})
     */
    private $userRates;

    private $rate;

    /**
     * @ORM\OneToMany(targetEntity="App\Entity\UserRate", mappedBy="author")
     */
    private $authorRates;


    public function __construct()
    {
        $this->setRoles(['ROLE_AUTHOR']);
        $this->userLibraries = new ArrayCollection();
        $this->comicLoans = new ArrayCollection();
        $this->userRequests = new ArrayCollection();
        $this->requestComicLoans = new ArrayCollection();
        $this->userRates = new ArrayCollection();
        $this->authorRates = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getRate(): ?string
    {
        $countRate = 0;
        $this->rate = 0;
        foreach ($this->getUserRates() as $rate) {
            $countRate += $rate->getRate();
        }
        if (count($this->getUserRates()) > 0) {
            $this->rate = (int)ceil($countRate / count($this->getUserRates()));
        }
        return $this->rate;
    }

    public function getPseudoname(): ?string
    {
        return $this->pseudoname;
    }

    public function setPseudoname(string $pseudoname): self
    {
        $this->pseudoname = $pseudoname;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string)$this->pseudoname;
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
        return (string)$this->password;
    }

    public function setPassword(string $password): self
    {
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
        // $this->plainPassword = null;
    }

    public function getFirstName(): ?string
    {
        return $this->firstName;
    }

    public function setFirstName(?string $firstName): self
    {
        $this->firstName = $firstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->lastName;
    }

    public function setLastName(?string $lastName): self
    {
        $this->lastName = $lastName;

        return $this;
    }

    public function getAge(): ?int
    {
        return $this->age;
    }

    public function setAge(?int $age): self
    {
        $this->age = $age;

        return $this;
    }

    public function getCity(): ?string
    {
        return $this->city;
    }

    public function setCity(string $city): self
    {
        $this->city = $city;

        return $this;
    }

    public function getDateCreated(): ?\DateTime
    {
        return $this->dateCreated;
    }

    public function setDateCreated(\DateTime $dateCreated): self
    {
        $this->dateCreated = $dateCreated;

        return $this;
    }

    public function getCountry(): ?string
    {
        return $this->country;
    }

    public function setCountry(string $country): self
    {
        $this->country = $country;

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

    /**
     * @return Collection|UserLibrary[]
     */
    public function getUserLibraries(): Collection
    {
        return $this->userLibraries;
    }

    public function addUserLibrary(UserLibrary $userLibrary): self
    {
        if (!$this->userLibraries->contains($userLibrary)) {
            $this->userLibraries[] = $userLibrary;
            $userLibrary->setUser($this);
        }

        return $this;
    }

    public function removeUserLibrary(UserLibrary $userLibrary): self
    {
        if ($this->userLibraries->contains($userLibrary)) {
            $this->userLibraries->removeElement($userLibrary);
            // set the owning side to null (unless already changed)
            if ($userLibrary->getUser() === $this) {
                $userLibrary->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|ComicLoan[]
     */
    public function getComicLoans(): Collection
    {
        return $this->comicLoans;
    }

    public function addComicLoan(ComicLoan $comicLoan): self
    {
        if (!$this->comicLoans->contains($comicLoan)) {
            $this->comicLoans[] = $comicLoan;
            $comicLoan->setUserLoaner($this);
        }

        return $this;
    }

    public function removeComicLoan(ComicLoan $comicLoan): self
    {
        if ($this->comicLoans->contains($comicLoan)) {
            $this->comicLoans->removeElement($comicLoan);
            // set the owning side to null (unless already changed)
            if ($comicLoan->getUserLoaner() === $this) {
                $comicLoan->setUserLoaner(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|RequestComicLoan[]
     */
    public function getRequestComicLoans(): Collection
    {
        return $this->requestComicLoans;
    }

    public function addRequestComicLoan(RequestComicLoan $requestComicLoan): self
    {
        if (!$this->requestComicLoans->contains($requestComicLoan)) {
            $this->requestComicLoans[] = $requestComicLoan;
            $requestComicLoan->setUser($this);
        }

        return $this;
    }

    public function removeRequestComicLoan(RequestComicLoan $requestComicLoan): self
    {
        if ($this->requestComicLoans->contains($requestComicLoan)) {
            $this->requestComicLoans->removeElement($requestComicLoan);
            // set the owning side to null (unless already changed)
            if ($requestComicLoan->getUser() === $this) {
                $requestComicLoan->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @param File|null $avatarPicture
     * @throws \Exception
     */
    public function setAvatarPicture(?File $avatarPicture = null): void
    {
        $this->avatarPicture = $avatarPicture;

        if (null !== $avatarPicture) {
            $this->updatedAt = new \DateTime();
        }
    }

    public function getAvatarPicture(): ?File
    {
        return $this->avatarPicture;
    }

    public function setAvatarName(?string $avatarName): void
    {
        $this->avatarName = $avatarName;
    }

    public function getAvatarName(): ?string
    {
        return $this->avatarName;
    }

    /**
     * @return Collection|UserRate[]
     */
    public function getUserRates(): Collection
    {
        return $this->userRates;
    }

    public function addUserRate(UserRate $userRate): self
    {
        if (!$this->userRates->contains($userRate)) {
            $this->userRates[] = $userRate;
            $userRate->setUser($this);
        }

        return $this;
    }

    public function removeUserRate(UserRate $userRate): self
    {
        if ($this->userRates->contains($userRate)) {
            $this->userRates->removeElement($userRate);
            // set the owning side to null (unless already changed)
            if ($userRate->getUser() === $this) {
                $userRate->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|UserRate[]
     */
    public function getAuthorRates(): Collection
    {
        return $this->authorRates;
    }

    public function addAuthorRate(UserRate $authorRate): self
    {
        if (!$this->authorRates->contains($authorRate)) {
            $this->authorRates[] = $authorRate;
            $authorRate->setAuthor($this);
        }

        return $this;
    }

    public function removeAuthorRate(UserRate $authorRate): self
    {
        if ($this->authorRates->contains($authorRate)) {
            $this->authorRates->removeElement($authorRate);
            // set the owning side to null (unless already changed)
            if ($authorRate->getAuthor() === $this) {
                $authorRate->setAuthor(null);
            }
        }

        return $this;
    }

}
