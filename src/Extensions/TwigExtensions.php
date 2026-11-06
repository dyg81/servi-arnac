<?php

namespace App\Extensions;

use App\Service\IdHasher;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class TwigExtensions extends AbstractExtension
{
    protected $id_hasher;

    public function getFunctions()
    {
        return [
            new TwigFunction('url_id_hasher_encode', [$this, 'UrlIdHasherEncode']),
            new TwigFunction('url_id_hasher_decode', [$this, 'UrlIdHasherDecode']),
        ];
    }

    public function __construct(IdHasher $hasherService)
    {
        $this->id_hasher = $hasherService;
    }

    /**
     * @param $value
     * @return string
     */
    public function urlIdHasherEncode($value): string
    {
        return $this->id_hasher->encode($value);
    }

    /**
     * @param $value
     * @return mixed|null
     */
    public function urlIdHasherDecode($value)
    {
        return $this->id_hasher->decode($value);
    }
}