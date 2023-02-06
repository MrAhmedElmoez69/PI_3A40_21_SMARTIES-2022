<?php

namespace App\Entity;

use Symfony\Component\Validator\Constraints as Assert;
class SearchProduct
{
    /**
     * @var int|null
     */
    private $minsurface;
    /**
     * @var int|null
     */
    private $maxprice;

    /**
     * @return int|null
     */
    public function getMaxprice(): ?int
    {
        return $this->maxprice;
    }

    /**
     * @param int|null $maxprice
     * @return SearchProduct
     */
    public function setMaxprice(int $maxprice): SearchProduct
    {
        $this->maxprice = $maxprice;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getMinsurface(): ?int
    {
        return $this->minsurface;
    }

    /**
     * @param int|null $minsurface
     * @return SearchProduct
     */
    public function setMinsurface(int $minsurface): SearchProduct
    {
        $this->minsurface = $minsurface;
        return $this;
    }


}
