<?php

namespace App\Entity;

use App\Repository\ContactRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;


#[ORM\Entity(repositoryClass: ContactRepository::class)]
class Contact
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
  

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El nombre es obligatorio')]
    private $FirstName;

    #[ORM\Column(type: "string", length: 255, nullable:true)]
    private $LastName;

    #[ORM\Column(type: "string", length: 255)]
    #[Assert\Email(message: "{{ value }} no es un correo válido")]
    private $Email;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El asunto es obligatorio')]
    private $Subject;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: 'El mensaje es obligatorio')]
    private $Message;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getFirstName(): ?string
    {
        return $this->FirstName;
    }

    public function setFirstName(string $FirstName): self
    {
        $this->FirstName = $FirstName;

        return $this;
    }

    public function getLastName(): ?string
    {
        return $this->LastName;
    }

    public function setLastName(?string $LastName): self
    {
        $this->LastName = $LastName;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->Email;
    }

    public function setEmail(string $Email): self
    {
        $this->Email = $Email;

        return $this;
    }

    public function getSubject(): ?string
    {
        return $this->Subject;
    }

    public function setSubject(string $Subject): self
    {
        $this->Subject = $Subject;

        return $this;
    }

    public function getMessage(): ?string
    {
        return $this->Message;
    }

    public function setMessage(string $Message): self
    {
        $this->Message = $Message;

        return $this;
    }
}
