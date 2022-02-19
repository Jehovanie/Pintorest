<?php

namespace App\Entity\Traits;


trait Timestampable
{

    /**
     * @ORM\Column(type="datetime" , options={"default" : "CURRENT_TIMESTAMP"})
     */
    private $createdAt;

    /**
     * @ORM\Column(type="datetime" , options={"default" : "CURRENT_TIMESTAMP"})
     */
    private $updateAt;

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateTimestamp(): void
    {
        if ($this->getCreatedAt() === null) {
            $this->setCreatedAt(new \DateTimeImmutable());
        }

        $this->setUpdateAt(new \DateTimeImmutable());
    }
}
