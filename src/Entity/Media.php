<?php

namespace App\Entity;

use App\Repository\MediaRepository;
use Doctrine\ORM\Mapping as ORM;

use ApiPlatform\OpenApi\Model;
use ApiPlatform\Metadata\ApiProperty;
use App\Controller\CreateMediaObjectAction;
use Symfony\Component\HttpFoundation\File\File;
use Vich\UploaderBundle\Mapping\Annotation as Vich;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Delete;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Serializer\Attribute\Groups;
 
#[Vich\Uploadable]
#[ApiResource(
    forceEager: false,
    normalizationContext: ['groups' => ['read']],
    types: ['https://schema.org/MediaObject'],
    operations: [
        new Get(security: "is_granted('ROLE_VETERINARIAN') or is_granted('ROLE_ASSISTANT')", securityMessage: 'You are not allowed to get this media'),
        new GetCollection(security: "is_granted('ROLE_VETERINARIAN') or is_granted('ROLE_ASSISTANT')", securityMessage: 'You are not allowed to get medias'),
        new Delete(security: "is_granted('ROLE_VETERINARIAN') or is_granted('ROLE_ASSISTANT')", securityMessage: 'You are not allowed to delete this media'),
        new Post(
            security: "is_granted('ROLE_VETERINARIAN') or is_granted('ROLE_ASSISTANT')",
            securityMessage: 'You are not allowed to add medias',
            controller: CreateMediaObjectAction::class,
            deserialize: false,
            validationContext: ['groups' => ['Default', 'write']],
            openapi: new Model\Operation(
                requestBody: new Model\RequestBody(
                    content: new \ArrayObject([
                        'multipart/form-data' => [
                            'schema' => [
                                'type' => 'object',
                                'properties' => [
                                    'file' => [
                                        'type' => 'string',
                                        'format' => 'binary'
                                    ]
                                ]
                            ]
                        ]
                    ])
                )
            )
        )
    ]
)]

#[ORM\Entity(repositoryClass: MediaRepository::class)]
class Media
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups('read')]
    private ?int $id = null;

    #[ORM\Column(length: 255, nullable: true)]
    #[Groups(['read', 'write'])]
    private ?string $filePath = null;

    #[ORM\OneToOne(mappedBy: 'picture', cascade: ['persist', 'remove'])]
    #[Groups(['read', 'write'])]
    private ?Animal $animal = null;

    #[ApiProperty(types: ['https://schema.org/contentUrl'])]
    #[Groups(['read'])]
    public ?string $contentUrl = null;

    #[Vich\UploadableField(mapping: 'media_object', fileNameProperty: 'filePath')]
    #[Assert\NotNull(groups: ['write'])]
    public ?File $file = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFilePath(): ?string
    {
        return $this->filePath;
    }

    public function setFilePath(?string $filePath): static
    {
        $this->filePath = $filePath;

        return $this;
    }

    public function getAnimal(): ?Animal
    {
        return $this->animal;
    }

    public function setAnimal(?Animal $animal): static
    {
        // unset the owning side of the relation if necessary
        if ($animal === null && $this->animal !== null) {
            $this->animal->setPicture(null);
        }

        // set the owning side of the relation if necessary
        if ($animal !== null && $animal->getPicture() !== $this) {
            $animal->setPicture($this);
        }

        $this->animal = $animal;

        return $this;
    }
}
