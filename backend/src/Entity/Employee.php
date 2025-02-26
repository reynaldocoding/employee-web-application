<?php

namespace App\Entity;

use App\Repository\EmployeeRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\HasLifecycleCallbacks;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Serializer\Attribute\Context;
use Symfony\Component\Serializer\Normalizer\DateTimeNormalizer;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: EmployeeRepository::class)]
#[UniqueEntity('email', message: "El correo electrónico ya está en uso.")]
#[HasLifecycleCallbacks]
class Employee
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "El nombre es obligatorio")]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "El nombre debe tener al menos {{ limit }} caracteres",
        maxMessage: "El nombre no puede tener más de {{ limit }} caracteres"
    )]
    private ?string $name = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Los apellidos son obligatorios")]
    #[Assert\Length(
        min: 2,
        max: 50,
        minMessage: "Los apellidos deben tener al menos {{ limit }} caracteres",
        maxMessage: "Los apellidos no pueden tener más de {{ limit }} caracteres"
    )]
    private ?string $lastNames = null;

    #[ORM\Column(length: 50)]
    #[Assert\NotBlank(message: "Se requiere la información del puesto de trabajo")]
    private ?string $position = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    #[Context([DateTimeNormalizer::FORMAT_KEY => 'Y-m-d'])]
    #[Assert\NotBlank(message: "Se requiere la fecha de nacimiento")]
    private ?\DateTimeInterface $birthdate = null;

    #[ORM\Column(length: 180, unique: true)]
    #[Assert\NotBlank(message: "Se requiere el correo electrónico")]
    #[Assert\Email(
        message: 'El correo electrónico {{ value }} no es un correo electrónico válido.'
    )]
    #[Assert\Length(max: 180)]
    private ?string $email = null;

    #[ORM\Column]
    private ?\DateTimeImmutable $created_at = null;

    #[ORM\Column(nullable: true)]
    private ?\DateTimeImmutable $updated_at = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getLastNames(): ?string
    {
        return $this->lastNames;
    }

    public function setLastNames(string $lastNames): static
    {
        $this->lastNames = $lastNames;

        return $this;
    }

    public function getFullName(): ?string
    {
        return $this->name . ' ' . $this->lastNames;
    }

    public function getPosition(): ?string
    {
        return $this->position;
    }

    public function setPosition(string $position): static
    {
        $this->position = $position;

        return $this;
    }

    public function getBirthdate(): ?\DateTimeInterface
    {
        return $this->birthdate;
    }

    public function setBirthdate(\DateTimeInterface $birthdate): static
    {
        $this->birthdate = $birthdate;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->created_at;
    }

    public function setCreatedAt(\DateTimeImmutable $created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    public function getUpdatedAt(): ?\DateTimeImmutable
    {
        return $this->updated_at;
    }

    public function setUpdatedAt(?\DateTimeImmutable $updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }

    #[ORM\PrePersist]
    public function setCreatedAtValue(): void
    {
        $this->created_at = new \DateTimeImmutable();
        $this->setUpdatedAtValue();
    }

    #[ORM\PreUpdate]
    public function setUpdatedAtValue(): void
    {
        $this->updated_at = new \DateTimeImmutable();
    }
}
