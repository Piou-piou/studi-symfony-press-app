<?php

namespace App\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\Column]
    private string $firstname;
    #[ORM\Column]
    private string $lastname;

    #[ORM\Column]
    private string $username;

    #[ORM\Column]
    private string $password;

    #[ORM\Column(type: 'json')]
    private array $roles = [];

    #[ORM\OneToMany(targetEntity: ArticleNote::class, mappedBy: 'User', orphanRemoval: true)]
    private Collection $articleNotes;

    public function __construct()
    {
        $this->articleNotes = new ArrayCollection();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): void
    {
        $this->id = $id;
    }

    public function getFirstname(): string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname): self
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname): self
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getUsername(): string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

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

    public function eraseCredentials(): void
    {
        return;
    }

    public function getUserIdentifier(): string
    {
        return $this->username;
    }

    /**
     * @return Collection<int, ArticleNote>
     */
    public function getArticleNotes(): Collection
    {
        return $this->articleNotes;
    }

    public function addArticleNote(ArticleNote $articleNote): static
    {
        if (!$this->articleNotes->contains($articleNote)) {
            $this->articleNotes->add($articleNote);
            $articleNote->setUser($this);
        }

        return $this;
    }

    public function removeArticleNote(ArticleNote $articleNote): static
    {
        if ($this->articleNotes->removeElement($articleNote)) {
            // set the owning side to null (unless already changed)
            if ($articleNote->getUser() === $this) {
                $articleNote->setUser(null);
            }
        }

        return $this;
    }
}
