<?php

namespace App\Entity;

use App\Repository\ContestantsRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=ContestantsRepository::class)
 */
class Contestants
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=UserAccounts::class, inversedBy="joined_contests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user_id;

    /**
     * @ORM\ManyToOne(targetEntity=Contest::class, inversedBy="my_contestants")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contest;

    /**
     * @ORM\Column(type="integer")
     */
    private $photo_count;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUserId(): ?UserAccounts
    {
        return $this->user_id;
    }

    public function setUserId(?UserAccounts $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }

    public function getContest(): ?Contest
    {
        return $this->contest;
    }

    public function setContest(?Contest $contest): self
    {
        $this->contest = $contest;

        return $this;
    }

    public function getPhotoCount(): ?int
    {
        return $this->photo_count;
    }

    public function setPhotoCount(int $photo_count): self
    {
        $this->photo_count = $photo_count;

        return $this;
    }
}
