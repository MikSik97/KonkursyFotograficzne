<?php

namespace App\Entity;

use App\Repository\UserAccountsRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * @ORM\Entity(repositoryClass=UserAccountsRepository::class)
 */
class UserAccounts implements UserInterface
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=180, unique=true)
     */
    private $email;

    /**
     * @ORM\Column(type="json")
     */
    private $roles = [];

    /**
     * @var string The hashed password
     * @ORM\Column(type="string")
     */
    private $password;

    /**
     * @ORM\OneToMany(targetEntity=Organizer::class, mappedBy="user_id")
     */
    private $my_contests;

    /**
     * @ORM\OneToMany(targetEntity=Contestants::class, mappedBy="user_id")
     */
    private $joined_contests;

    /**
     * @ORM\OneToMany(targetEntity=Photo::class, mappedBy="author")
     */
    private $my_photos;

    /**
     * @ORM\OneToMany(targetEntity=VoteLog::class, mappedBy="author")
     */
    private $my_logs;

    /**
     * @ORM\OneToMany(targetEntity=Judge::class, mappedBy="judge")
     */
    private $where_im_judge;

    public function __construct()
    {
        $this->my_contests = new ArrayCollection();
        $this->joined_contests = new ArrayCollection();
        $this->my_photos = new ArrayCollection();
        $this->my_logs = new ArrayCollection();
        $this->where_im_judge = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }
    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): self
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getPassword(): string
    {
        return (string) $this->password;
    }

    public function setPassword(string $password): self
    {
        $this->password = $password;

        return $this;
    }

    /**
     * @see UserInterface
     */
    public function getSalt()
    {
        // not needed when using the "bcrypt" algorithm in security.yaml
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials()
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection|Organizer[]
     */
    public function getMyContests(): Collection
    {
        return $this->my_contests;
    }

    public function addMyContest(Organizer $myContest): self
    {
        if (!$this->my_contests->contains($myContest)) {
            $this->my_contests[] = $myContest;
            $myContest->setUserId($this);
        }

        return $this;
    }

    public function removeMyContest(Organizer $myContest): self
    {
        if ($this->my_contests->removeElement($myContest)) {
            // set the owning side to null (unless already changed)
            if ($myContest->getUserId() === $this) {
                $myContest->setUserId(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|Contestants[]
     */
    public function getJoinedContests(): Collection
    {
        return $this->joined_contests;
    }

    public function addJoinedContest(Contestants $joinedContest): self
    {
        if (!$this->joined_contests->contains($joinedContest)) {
            $this->joined_contests[] = $joinedContest;
            $joinedContest->setUserId($this);
        }

        return $this;
    }

    public function removeJoinedContest(Contestants $joinedContest): self
    {
        if ($this->joined_contests->removeElement($joinedContest)) {
            // set the owning side to null (unless already changed)
            if ($joinedContest->getUserId() === $this) {
                $joinedContest->setUserId(null);
            }
        }

        return $this;
    }
    /**
     * @return Collection|Photo[]
     */
    public function getMyPhotos(): Collection
    {
        return $this->my_photos;
    }

    public function addMyPhoto(Photo $myPhoto): self
    {
        if (!$this->my_photos->contains($myPhoto)) {
            $this->my_photos[] = $myPhoto;
            $myPhoto->setAuthor($this);
        }

        return $this;
    }

    public function removeMyPhoto(Photo $myPhoto): self
    {
        if ($this->my_photos->removeElement($myPhoto)) {
            // set the owning side to null (unless already changed)
            if ($myPhoto->getAuthor() === $this) {
                $myPhoto->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection|VoteLog[]
     */
    public function getMyLogs(): Collection
    {
        return $this->my_logs;
    }

    public function addMyLog(VoteLog $myLog): self
    {
        if (!$this->my_logs->contains($myLog)) {
            $this->my_logs[] = $myLog;
            $myLog->addAuthor($this);
        }

        return $this;
    }

    public function removeMyLog(VoteLog $myLog): self
    {
        if ($this->my_logs->removeElement($myLog)) {
            $myLog->removeAuthor($this);
        }

        return $this;
    }

    /**
     * @return Collection|Judge[]
     */
    public function getWhereImJudge(): Collection
    {
        return $this->where_im_judge;
    }

    public function addWhereImJudge(Judge $whereImJudge): self
    {
        if (!$this->where_im_judge->contains($whereImJudge)) {
            $this->where_im_judge[] = $whereImJudge;
            $whereImJudge->setJudge($this);
        }

        return $this;
    }

    public function removeWhereImJudge(Judge $whereImJudge): self
    {
        if ($this->where_im_judge->removeElement($whereImJudge)) {
            // set the owning side to null (unless already changed)
            if ($whereImJudge->getJudge() === $this) {
                $whereImJudge->setJudge(null);
            }
        }

        return $this;
    }
}
