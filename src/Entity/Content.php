<?php

namespace App\Entity;

use App\GraphQL\PrimitiveType;
use App\Repository\ContentRepository;
use Doctrine\ORM\Mapping as ORM;
use Overblog\GraphQLBundle\Annotation as GQL;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: ContentRepository::class)]
#[GQL\Type()]
class Content
{
    final public const GROUP_GET = 'get_content';
    final public const TYPE = 'Content';

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    #[Groups([self::GROUP_GET])]
    #[GQL\Field(type: PrimitiveType::INT_REQUIRED)]
    private ?int $id = null;

    #[ORM\Column(type: 'string', length: 255)]
    #[GQL\Field(type: PrimitiveType::STRING_REQUIRED)]
    #[Groups([self::GROUP_GET])]
    private ?string $slug = null;

    #[ORM\Column(type: 'text')]
    #[GQL\Field(type: PrimitiveType::STRING_REQUIRED)]
    #[Groups([self::GROUP_GET])]
    private ?string $content = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }

    public function getContent(): ?string
    {
        return $this->content;
    }

    public function setContent(string $content): self
    {
        $this->content = $content;

        return $this;
    }
}
