<?php


namespace App\Traits;

use Doctrine\ORM\Mapping as ORM;

/**
 * Trait EntityDateInformationTrait
 * @Gedmo\SoftDeleteable(fieldName="deletedAt", timeAware=false)
 */
trait EntitySearchTrait
{
    /**
     * @ORM\Column(type="text", nullable=true)
     */
    private $search;

    /**
     * @return string
     */
    public function getSearch()
    {
        return $this->search;
    }

    /**
     * @param mixed $search
     */
    public function setSearch($search): void
    {
        $this->search = $search;
    }
}
