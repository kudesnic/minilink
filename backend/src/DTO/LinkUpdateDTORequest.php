<?php

namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Entity\Link;

class LinkUpdateDTORequest extends DTORequestAbstract
{

    /**
     * @Assert\Regex("/(https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|www\.[a-zA-Z0-9][a-zA-Z0-9-]+[a-zA-Z0-9]\.[^\s]{2,}|https?:\/\/(?:www\.|(?!www))[a-zA-Z0-9]+\.[^\s]{2,}|www\.[a-zA-Z0-9]+\.[^\s]{2,})/"
     * )
     */
    public $link;

    /**
     * @Assert\Type("integer")
     */
    public $expiration_time;

    /**
     * @Assert\Type("string")
     * @Assert\Regex("/active|disabled|expired/")
     */
    public $status;

}