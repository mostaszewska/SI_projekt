<?php

namespace App\Entity;

use App\Repository\AnswerRepository;
use Doctrine\ORM\Mapping as ORM;
use DateTimeInterface;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Entity(repositoryClass=AnswerRepository::class)
 * @ORM\Table(name="answers")
 */
class Answer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * Created at.
     *
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="create")
     */
    private $createdAt;

    /**
     * Updated at.
     *
     * @var DateTimeInterface
     *
     * @ORM\Column(type="datetime")
     *
     * @Gedmo\Timestampable(on="update")
     */
    private $updatedAt;

    /**
     * Text.
     *
     * @var string
     *
     * @ORM\Column(
     *     type="string",
     *     length=255,
     * )
     * @ORM\JoinColumn(nullable=false)
     *
     * @Assert\Type(type="string")
     * @Assert\NotBlank
     * @Assert\Length(
     *     min="10",
     *     max="255",
     * )
     */
    private $text;

    /**
     * @ORM\Column(type="boolean", nullable=true)
     */
    private $favourite;

    /**
     * @ORM\ManyToOne(targetEntity=Question::class, inversedBy="answers")
     */
    private $question;

    /**
     * Author.
     *
     * @var \App\Entity\User
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\User")
     * @ORM\JoinColumn(nullable=true)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=64, nullable=true)
     *
     * @Assert\Length(
     *     max="64"
     * )
     */
    private $nick;

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * Getter for Created At.
     *
     * @return \DateTimeInterface|null Created at
     */
    public function getCreatedAt(): ?DateTimeInterface
    {
        return $this->createdAt;
    }

    /**
     * Setter for Created at.
     *
     * @param \DateTimeInterface|null $createdAt Created at
     */
    public function setCreatedAt(DateTimeInterface $createdAt): void
    {
        $this->createdAt = $createdAt;
    }

    /**
     * Getter for Updated at.
     *
     * @return \DateTimeInterface|null Updated at
     */
    public function getUpdatedAt(): ?DateTimeInterface
    {
        return $this->updatedAt;
    }

    /**
     * Setter for Updated at.
     *
     * @param \DateTimeInterface $updatedAt Updated at
     */
    public function setUpdatedAt(DateTimeInterface $updatedAt): void
    {
        $this->updatedAt = $updatedAt;
    }

    public function getText(): ?string
    {
        return $this->text;
    }

    public function setText(?string $text): self
    {
        $this->text = $text;

        return $this;
    }

    public function getFavourite(): ?bool
    {
        return $this->favourite;
    }

    public function setFavourite(bool $favourite): self
    {
        $this->favourite = $favourite;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): self
    {
        $this->author = $author;

        return $this;
    }

    /**
     * Getter for Question.
     *
     * @return Question|null
     */
    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    /**
     * Setter for Question.
     *
     * @param Question|null $question
     *
     * @return void
     */
    public function setQuestion(?Question $question): void
    {
        $this->question = $question;
    }

    /**
     * Getter for Nick
     *
     * @return Nick|null
     */
    public function getNick(): ?string
    {
        return $this->nick;
    }
    /**
     * Setter for Nick
     *
     * @param Nick|null $nick
     */
    public function setNick(string $nick): void
    {
        $this->nick = $nick;
    }
}
