<?php

namespace App\Entity;

use App\Repository\JudgeRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=JudgeRepository::class)
 */
class Judge
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=UserAccounts::class, inversedBy="where_im_judge")
     * @ORM\JoinColumn(nullable=false)
     */
    private $judge;

    /**
     * @ORM\ManyToOne(targetEntity=Contest::class, inversedBy="my_judges")
     * @ORM\JoinColumn(nullable=false)
     */
    private $contest;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getJudge(): ?UserAccounts
    {
        return $this->judge;
    }

    public function setJudge(?UserAccounts $judge): self
    {
        $this->judge = $judge;

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
}
