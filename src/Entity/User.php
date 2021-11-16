<?php

namespace App\Entity;

use ApiPlatform\Core\Annotation\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Core\Annotation\ApiProperty;
use App\Repository\UserRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Serializer\Annotation\SerializedName;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Annotation\Ignore;

/**
 * @ORM\Entity(repositoryClass=UserRepository::class)
 * @ORM\Table(name="`user`")
 */
#[UniqueEntity(fields: "email", message: "Email Already Exists")]

#[ApiResource(
    collectionOperations: [
        "get",
        "post"
    ],
    itemOperations: [
        "get",
        "put" => [
            "security" => "is_granted('ROLE_ADMIN') or object == user",
            "security_message" => "Only admins and current logged in user can make put request."
        ],
        "DELETE" => [
            "security" => "is_granted('ROLE_ADMIN') or object == user",
            "security_message" => "Only admins and current logged in user can make delete request."
        ]
    ]
)]

class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     * @ApiProperty(security="is_granted('ROLE_ADMIN') or object == user")
     */
    #[Groups(["admin:read", "admin:write"])]
    private ?string $email;

    /**
     * @ORM\Column(type="json")
     * @ApiProperty(security="is_granted('ROLE_ADMIN') or object == user")
     */
    #[Groups(["admin:read", "admin:write"])]
    private array $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private string $password;

    /**
     * @ApiProperty(security="is_granted('ROLE_ADMIN') or object == user")
     */
    #[SerializedName('password')]
    #[Groups(["admin:read", "admin:write"])]
    private ?string $plainPassword = null;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    #[Groups(["admin:read", "admin:write"])]
    private ?string $name;

    /**
     * @ORM\Column(type="string", length=150, nullable=true)
     */
    #[Groups(["admin:read", "admin:write"])]
    private string $username;

    /**
     * @ORM\Column(type="string", length=100, nullable=true)
     */
    #[Groups(["admin:read", "admin:write"])]
    private ?string $phone;

    /**
     * @var string Property viewable and writable only by users with ROLE_ADMIN
     * @ORM\Column(type="string", length=150, nullable=true)
     * @ApiProperty(security="is_granted('ROLE_ADMIN')")
     */
    #[Groups(["admin:read", "admin:write"])]
    private ?string $adminOnlyProperty;

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
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
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

    public function getPlainPassword(): ?string
    {
        return $this->plainPassword;
    }
    public function setPlainPassword(?string $plainPassword): self
    {
        $this->plainPassword = $plainPassword;
        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        $this->plainPassword = null;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function setUsername(?string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPhone(): ?string
    {
        return $this->phone;
    }

    public function setPhone(?string $phone): self
    {
        $this->phone = $phone;

        return $this;
    }

    public function getAdminOnlyProperty(): ?string
    {
        return $this->adminOnlyProperty;
    }

    public function setAdminOnlyProperty(?string $adminOnlyProperty): self
    {
        $this->adminOnlyProperty = $adminOnlyProperty;

        return $this;
    }
}