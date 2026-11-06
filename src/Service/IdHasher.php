<?php

namespace App\Service;

use Hashids\Hashids;

class IdHasher
{
    private $HashIdsInstance;


    /**
     * @param string $entropyString
     * @param int $hashPadding
     * @param string $hashAlphabet
     */
    public function __construct(string $entropyString, int $hashPadding, string $hashAlphabet)
    {
        $this->HashIdsInstance = new Hashids($entropyString, $hashPadding, $hashAlphabet);
    }

    /**
     * @param $number
     * @return string
     */
    public function encode($number): string
    {
        return $this->HashIdsInstance->encode($number);
    }

    /**
     * @param $hash
     * @return mixed|null
     */
    public function decode($hash)
    {
        $result = $this->HashIdsInstance->decode($hash);

        return empty($result) ? null : $result[0];
    }
}