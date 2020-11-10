<?php

namespace App\Entity;

use App\Repository\VisitRepository;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

/**
 * @ORM\Entity(repositoryClass=VisitRepository::class)
 * Below part is needed only if we want to have readable indexes. Postgre by default generates indexes for FK
 * @ORM\Table(name="visit",indexes={@ORM\Index(name="link_idx", columns={"link_id"}),
 *     @ORM\Index(name="platform_idx", columns={"platform_id"}),
 *     @ORM\Index(name="country_idx", columns={"country_id"})}
 *     )
 */
class Visit
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     * @Groups("APIGroup")
     */
    private $id;

    /**
     * @ORM\ManyToOne(targetEntity=Link::class, inversedBy="visits")
     * @ORM\JoinColumn(nullable=false)
     */
    private $link;

    /**
     * @ORM\Column(type="string", length=255)
     * @Groups("APIGroup")
     */
    private $user_agent;

    /**
     * @ORM\Column(type="string", length=255, nullable=true)
     * @Groups("APIGroup")
     */
    private $referer;

    /**
     * @ORM\Column(type="string", length=15)
     * @Groups("APIGroup")
     */
    private $ip;

    /**
     * @ORM\ManyToOne(targetEntity=Country::class, inversedBy="visits")
     * @Groups("APIGroup")
     */
    private $country;

    /**
     * @ORM\ManyToOne(targetEntity=Platform::class, inversedBy="visits")
     * @Groups("APIGroup")
     */
    private $platform;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLink(): ?Link
    {
        return $this->link;
    }

    public function setLink(?Link $link): self
    {
        $this->link = $link;

        return $this;
    }

    public function getUserAgent(): ?string
    {
        return $this->user_agent;
    }

    public function setUserAgent(string $user_agent): self
    {
        $this->user_agent = $user_agent;

        return $this;
    }

    public function getReferer(): ?string
    {
        return $this->referer;
    }

    public function setReferer(?string $referer): self
    {
        $this->referer = $referer;

        return $this;
    }

    public function getIp(): ?string
    {
        return $this->ip;
    }

    public function setIp(string $ip): self
    {
        $this->ip = $ip;

        return $this;
    }

    public function getCountry(): ?Country
    {
        return $this->country;
    }

    public function setCountry(?Country $country): self
    {
        $this->country = $country;

        return $this;
    }

    public function getPlatform(): ?Platform
    {
        return $this->platform;
    }

    public function setPlatform(?Platform $platform): self
    {
        $this->platform = $platform;

        return $this;
    }
}
