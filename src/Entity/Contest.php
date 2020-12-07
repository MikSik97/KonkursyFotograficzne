<?php

namespace App\Entity;

use App\Repository\ContestRepository;
use DateTimeInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContestRepository::class)
 */
class Contest
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $user_limit;

    /**
     * @ORM\Column(type="integer")
     */
    private $photo_limit;

    /**
     * @ORM\Column(type="datetime")
     */
    private $applications_deadline;

    /**
     * @ORM\Column(type="datetime")
     */
    private $vote_start_time;

    /**
     * @ORM\Column(type="datetime")
     */
    private $vote_end_time;

    /**
     * @ORM\Column(type="string", length=255)
     */
    private $theme;

    /**
     * @ORM\OneToMany(targetEntity=Organizer::class, mappedBy="contest")
     */
    private $my_organizers;

    /**
     * @ORM\OneToMany(targetEntity=Contestants::class, mappedBy="contest")
     */
    private $my_contestants;

    /**
     * @ORM\OneToMany(targetEntity=Photo::class, mappedBy="contest")
     */
    private $applied_photos;

    /**
     * @ORM\Column(type="string", length=255, unique=true)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Judge::class, mappedBy="contest")
     */
    private $my_judges;

    public function __construct()
    {
        $this->my_organizers = new ArrayCollection();
        $this->my_contestants = new ArrayCollection();
        $this->applied_photos = new ArrayCollection();
        $this->my_judges = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserLimit(): ?int
    {
        return $this->user_limit;
    }

    public function setUserLimit(int $user_limit): self
    {
        $this->user_limit = $user_limit;

        return $this;
    }

    public function getPhotoLimit(): ?int
    {
        return $this->photo_limit;
    }

    public function setPhotoLimit(int $photo_limit): self
    {
        $this->photo_limit = $photo_limit;

        return $this;
    }

    public function getApplicationsDeadline(): ?DateTimeInterface
    {
        return $this->applications_deadline;
    }

    public function setApplicationsDeadline(DateTimeInterface $applications_deadline): self
    {
        $this->applications_deadline = $applications_deadline;

        return $this;
    }

    public function getVoteStartTime(): ?DateTimeInterface
    {
        return $this->vote_start_time;
    }

    public function setVoteStartTime(DateTimeInterface $vote_start_time): self
    {
        $this->vote_start_time = $vote_start_time;

        return $this;
    }

    public function getVoteEndTime(): ?DateTimeInterface
    {
        return $this->vote_end_time;
    }

    public function setVoteEndTime(DateTimeInterface $vote_end_time): self
    {
        $this->vote_end_time = $vote_end_time;

        return $this;
    }

    public function getTheme(): ?string
    {
        return $this->theme;
    }

    public function setTheme(string $theme): self
    {
        $this->theme = $theme;

        return $this;
    }

    /**
     * @return Collection|Organizer[]
     */
    public function getMyOrganizers(): Collection
    {
        return $this->my_organizers;
    }

    public function addMyOrganizer(Organizer $myOrganizer): self
    {
        if (!$this->my_organizers->contains($myOrganizer)) {
            $this->my_organizers[] = $myOrganizer;
            $myOrganizer->setContest($this);
        }

        return $this;
    }

    public function removeMyOrganizer(Organizer $myOrganizer): self
    {
        if ($this->my_organizers->removeElement($myOrganizer)) {
            // set the owning side to null (unless already changed)
            if ($myOrganizer->getContest() === $this) {
                $myOrganizer->setContest(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Contestants[]
     */
    public function getMyContestants(): Collection
    {
        return $this->my_contestants;
    }

    public function addMyContestant(Contestants $myContestant): self
    {
        if (!$this->my_contestants->contains($myContestant)) {
            $this->my_contestants[] = $myContestant;
            $myContestant->setContest($this);
        }

        return $this;
    }

    public function removeMyContestant(Contestants $myContestant): self
    {
        if ($this->my_contestants->removeElement($myContestant)) {
            // set the owning side to null (unless already changed)
            if ($myContestant->getContest() === $this) {
                $myContestant->setContest(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Photo[]
     */
    public function getAppliedPhotos(): Collection
    {
        return $this->applied_photos;
    }

    public function addAppliedPhoto(Photo $appliedPhoto): self
    {
        if (!$this->applied_photos->contains($appliedPhoto)) {
            $this->applied_photos[] = $appliedPhoto;
            $appliedPhoto->addContest($this);
        }

        return $this;
    }

    public function removeAppliedPhoto(Photo $appliedPhoto): self
    {
        if ($this->applied_photos->removeElement($appliedPhoto)) {
            $appliedPhoto->removeContest($this);
        }

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection|Judge[]
     */
    public function getMyJudges(): Collection
    {
        return $this->my_judges;
    }

    public function addMyJudge(Judge $myJudge): self
    {
        if (!$this->my_judges->contains($myJudge)) {
            $this->my_judges[] = $myJudge;
            $myJudge->setContest($this);
        }

        return $this;
    }

    public function removeMyJudge(Judge $myJudge): self
    {
        if ($this->my_judges->removeElement($myJudge)) {
            // set the owning side to null (unless already changed)
            if ($myJudge->getContest() === $this) {
                $myJudge->setContest(null);
            }
        }

        return $this;
    }
}
