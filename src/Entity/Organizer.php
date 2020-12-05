<?php

namespace App\Entity;

use App\Repository\OrganizerRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=OrganizerRepository::class)
 */
class Organizer
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Contest::class, inversedBy="my_organizers")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contest;

    /**
     * @ORM\ManyToOne(targetEntity=UserAccounts::class, inversedBy="my_contests")
     * @ORM\JoinColumn(nullable=false)
     */
    private $user_id;

    public function getId(): ?int
    {
        return $this->id;
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

    public function getUserId(): ?UserAccounts
    {
        return $this->user_id;
    }

    public function setUserId(?UserAccounts $user_id): self
    {
        $this->user_id = $user_id;

        return $this;
    }
}
