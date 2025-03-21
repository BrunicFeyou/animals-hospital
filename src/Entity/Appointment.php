<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\AppointmentStateEnum;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use App\Repository\AppointmentRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Attribute\Groups;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;

#[ApiResource(
    operations: [
        new GetCollection(security: "is_granted('ROLE_VETERINIAN')", securityMessage: 'You are not allowed to get appointments'),
        new Post(security: "is_granted('ROLE_ASSISTANT', 'ROLE_VETERINIAN')", securityMessage: 'You are not allowed to add appointments'),
        new Get(security: "is_granted('ROLE_VETERINIAN')", securityMessage: 'You are not allowed to get this appointment'),
        new Patch(security: "is_granted('ROLE_VETERINIAN')", securityMessage: 'You are not allowed to update this appointment'),
        new Delete(security: "is_granted('ROLE_VETERINIAN')", securityMessage: 'You are not allowed to delete this appointment'),
    ]
)]
#[ORM\Entity(repositoryClass: AppointmentRepository::class)]
class Appointment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('read')]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Groups('read')]
    private ?\DateTimeInterface $createdDate = null;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    #[Assert\NotBlank]
    #[Groups(['read', 'write'])]
    private ?\DateTimeInterface $appointmentDate = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank]
    #[Groups(['read', 'write'])]
    private ?string $reason = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read', 'write'])]
    private ?Animal $animal = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['read', 'write'])]
    private ?User $veterinary = null;

    #[ORM\ManyToOne(inversedBy: 'appointments')]
    #[Groups(['read', 'write'])]
    private ?User $assistant = null;

    #[ORM\Column(length: 255)]
    #[Groups(['read', 'write'])]
    private ?string $state = null;

    /**
     * @var Collection<int, Treatment>
     */
    #[ORM\ManyToMany(targetEntity: Treatment::class, inversedBy: 'appointments')]
    private Collection $treatments;

    public function __construct()
    {
        $this->treatments = new ArrayCollection();
        $this->createdDate = new \DateTime(
            'now',
            new \DateTimeZone('Europe/Paris')
        );
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCreatedDate(): ?\DateTimeInterface
    {
        return $this->createdDate ;
    }

    public function setCreatedDate(\DateTimeInterface $createdDate): static
    {
        $this->createdDate = $createdDate;

        return $this;
    }

    public function getAppointmentDate(): ?\DateTimeInterface
    {
        return $this->appointmentDate;
    }

    public function setAppointmentDate(\DateTimeInterface $appointmentDate): static
    {
        $this->appointmentDate = $appointmentDate;

        return $this;
    }

    public function getReason(): ?string
    {
        return $this->reason;
    }

    public function setReason(string $reason): static
    {
        $this->reason = $reason;

        return $this;
    }

    public function getAnimal(): ?Animal
    {
        return $this->animal;
    }

    public function setAnimal(?Animal $animal): static
    {
        $this->animal = $animal;

        return $this;
    }

    public function getVeterinary(): ?User
    {
        return $this->veterinary;
    }

    public function setVeterinary(?User $veterinary): static
    {
        $this->veterinary = $veterinary;
        if(!in_array('ROLE_VETERINARIAN', $this->veterinary->getRoles())) {
            $name = $this->veterinary->getName();
            throw new \ValueError("$name is not a a user with ROLE_VETERINARIAN. Their roles are " . implode(", ", $this->veterinary->getRoles()));
        }

        return $this;
    }

    public function getAssistant(): ?User
    {
        return $this->assistant;
    }

    public function setAssistant(?User $assistant): static
    {
        $this->assistant = $assistant;
        if(!in_array('ROLE_ASSISTANT', $this->assistant->getRoles())) {
            $name = $this->assistant->getName();
            throw new \ValueError("$name is not a a user with ROLE_ASSISTANT. Their roles are " . implode(", ", $this->assistant->getRoles()));
        }
        return $this;
    }

    public function getState(): ?string
    {
        return $this->state;
    }

    public function setState(string $state): static
    {
        $this->state = $state;
        if($this->state == null || $this->state=="") {
            $this->state = AppointmentStateEnum::programmed->value;
        }

        $this->state = AppointmentStateEnum::fromValue($this->state);
        return $this;
    }

    /**
     * @return Collection<int, Treatment>
     */
    public function getTreatments(): Collection
    {
        return $this->treatments;
    }

    public function addTreatment(Treatment $treatment): static
    {
        if (!$this->treatments->contains($treatment)) {
            $this->treatments->add($treatment);
        }

        return $this;
    }

    public function removeTreatment(Treatment $treatment): static
    {
        $this->treatments->removeElement($treatment);

        return $this;
    }
}
