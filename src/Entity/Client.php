<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use App\Repository\ClientRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;


#[ApiResource(
    forceEager: false,
    operations: [
        new GetCollection(security: "is_granted('ROLE_VETERINARIAN') or is_granted('ROLE_ASSISTANT') or is_granted('ROLE_DIRECTOR')", securityMessage: 'You are not allowed to get users'),
        new Post(security: "is_granted('ROLE_VETERINARIAN') or is_granted('ROLE_ASSISTANT') or is_granted('ROLE_DIRECTOR')", securityMessage: 'You are not allowed to add clients'),
        new Get(security: "is_granted('ROLE_VETERINARIAN') or is_granted('ROLE_ASSISTANT') or is_granted('ROLE_DIRECTOR')", securityMessage: 'You are not allowed to get this client'),
        new Patch(security: "is_granted('ROLE_VETERINARIAN') or is_granted('ROLE_ASSISTANT') or is_granted('ROLE_DIRECTOR')", securityMessage: 'You are not allowed to update this client'),
        new Delete(security: "is_granted('ROLE_VETERINARIAN') or is_granted('ROLE_ASSISTANT') or is_granted('ROLE_DIRECTOR')", securityMessage: 'You are not allowed to delete this client'),
    ],
    normalizationContext: ['groups' => ['read']],
    denormalizationContext: ['groups' => ['write']]
)]
#[ORM\Entity(repositoryClass: ClientRepository::class)]
#[UniqueEntity('fullname')]
class Client
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('read')]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank]
    #[Groups(['read', 'write'])]
    private ?string $fullname = null;

     /**
     * @var Collection<int, Animal>
     */
    #[ORM\OneToMany(targetEntity: Animal::class, mappedBy: 'owner')]
    #[Groups(['read', 'write'])]
    private Collection $animals;

    public function __construct()
    {
        $this->animals = new ArrayCollection();
    
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFullname(): ?string
    {
        return $this->fullname;
    }

    public function setFullname(string $fullname): static
    {
        $this->fullname = $fullname;

        return $this;
    }

     /**
     * @return Collection<int, Animal>
     */
    public function getAnimals(): Collection
    {
        return $this->animals;
    }

    public function addAnimal(Animal $animal): static
    {
        if (!$this->animals->contains($animal)) {
            $this->animals->add($animal);
            $animal->setOwner($this);
        }

        return $this;
    }

    public function removeAnimal(Animal $animal): static
    {
        if ($this->animals->removeElement($animal)) {
            // set the owning side to null (unless already changed)
            if ($animal->getOwner() === $this) {
                $animal->setOwner(null);
            }
        }

        return $this;
    }
}
