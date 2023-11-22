<?php

namespace App\Entity;

use App\Repository\PostRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: PostRepository::class)]
class Post
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;
  
    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'El título es obligatorio')]
    private $Title;

    #[ORM\Column(type: "text")]
    #[Assert\NotBlank(message: 'El contenido es obligatorio')]
    private $Content;

    #[ORM\Column(type: "datetime")]
    private $PublishedAt;

    #[ORM\Column(length: 255)]
    private $Image;

    #[ORM\Column(length: 255)]
    private $Slug;

    #[ORM\Column(type: "integer")]
    private $NumLikes;

    #[ORM\Column(type: "integer")]
    private $NumComments;
    
    #[ORM\Column(type: "integer")]
    private $NumViews;

    #[ORM\OneToMany(mappedBy: 'post', targetEntity: Comment::class)]
    private Collection $comments;

    #[ORM\ManyToOne(inversedBy: 'posts')]
    private ?User $User = null;
    

    public function __construct()
    {
        $this->PublishedAt = new \DateTime();
        $this->comments = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->Title;
    }

    public function setTitle(string $Title): self
    {
        $this->Title = $Title;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->Content;
    }

    public function setContent(string $Content): self
    {
        $this->Content = $Content;

        return $this;
    }

    public function getPublishedAt(): ?\DateTimeInterface
    {
        return $this->PublishedAt;
    }

    public function setPublishedAt(\DateTimeInterface $PublishedAt): self
    {
        $this->PublishedAt = $PublishedAt;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->Image;
    }

    public function setImage(string $Image): self
    {
        $this->Image = $Image;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->Slug;
    }

    public function setSlug(string $Slug): self
    {
        $this->Slug = $Slug;

        return $this;
    }

    public function getNumLikes(): ?int
    {
        return $this->NumLikes;
    }

    public function setNumLikes(int $NumLikes): self
    {
        $this->NumLikes = $NumLikes;

        return $this;
    }

    public function getNumComments(): ?string
    {
        return $this->NumComments;
    }

    public function setNumComments(string $NumComments): self
    {
        $this->NumComments = $NumComments;

        return $this;
    }

    public function getNumViews()
    {
        return $this->NumViews;
    }
    public function setNumViews($NumViews)
    {
        $this->NumViews = $NumViews;

        return $this;
    }

    /**
     * @return Collection<int, Comment>
     */
    public function getComments(): Collection
    {
        return $this->comments;
    }

    public function addComment(Comment $comment): static
    {
        if (!$this->comments->contains($comment)) {
            $this->comments->add($comment);
            $comment->setPost($this);
        }

        return $this;
    }

    public function removeComment(Comment $comment): static
    {
        if ($this->comments->removeElement($comment)) {
            // set the owning side to null (unless already changed)
            if ($comment->getPost() === $this) {
                $comment->setPost(null);
            }
        }

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

}
