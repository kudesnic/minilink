<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Link;

class LinkInsertDTORequest extends DTORequestAbstract
{

    /**
     * @Assert\NotNull
     * @Assert\Regex("/(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9]" .
     *     "[a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}" .
     *     "|www\.[a-zA-Z0-9]+\.[^\s]{2,})/"
     * )
     */
    public $link;

    /**
     * @Assert\NotNull
     * @Assert\Type("integer")
     */
    public $living_time;

    /**
     * @Assert\Type("string")
     * @Assert\Regex(Link::STATUS_ACTIVE . '|' . Link::STATUS_DISABLED . '|' . Link::STATUS_EXPIRED)
     */
    public $status;

}