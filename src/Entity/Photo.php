<?php

namespace App\Entity;

use App\Repository\PhotoRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=PhotoRepository::class)
 */
class Photo
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=UserAccounts::class, inversedBy="my_photos")
     * @ORM\JoinColumn(nullable=false)
     */
    private $author;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $filepath;

    /**
     * @ORM\ManyToOne(targetEntity=Contest::class, inversedBy="applied_photos")
     */
    private $contest;

    /**
     * @ORM\Column(type="float")
     */
    private $score;

    /**
     * @ORM\OneToMany(targetEntity=VoteLog::class, mappedBy="photo")
     */
    private $my_scores;

    public function __construct()
    {
        $this->contest = new ArrayCollection();
        $this->my_scores = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getAuthor(): ?UserAccounts
    {
        return $this->author;
    }

    public function setAuthor(?UserAccounts $author): self
    {
        $this->author = $author;

        return $this;
    }

    public function getFilepath(): ?string
    {
        return $this->filepath;
    }

    public function setFilepath(string $filepath): self
    {
        $this->filepath = $filepath;

        return $this;
    }

    /**
     * @return Collection|Contest[]
     */
    public function getContest(): Collection
    {
        return $this->contest;
    }
    public function setContest(?Contest $contest): self
    {
        $this->contest = $contest;

        return $this;
    }
    public function addContest(Contest $contest): self
    {
        if (!$this->contest->contains($contest)) {
            $this->contest[] = $contest;
        }

        return $this;
    }

    public function removeContest(Contest $contest): self
    {
        $this->contest->removeElement($contest);

        return $this;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(float $score): self
    {
        $this->score = $score;

        return $this;
    }

    /**
     * @return Collection|VoteLog[]
     */
    public function getMyScores(): Collection
    {
        return $this->my_scores;
    }

    public function addMyScore(VoteLog $myScore): self
    {
        if (!$this->my_scores->contains($myScore)) {
            $this->my_scores[] = $myScore;
            $myScore->addPhoto($this);
        }

        return $this;
    }

    public function removeMyScore(VoteLog $myScore): self
    {
        if ($this->my_scores->removeElement($myScore)) {
            $myScore->removePhoto($this);
        }

        return $this;
    }
}
