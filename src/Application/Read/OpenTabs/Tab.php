<?php

declare(strict_types=1);

namespace Cafe\Application\Read\OpenTabs;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(name="read_model_tab")
 */
class Tab
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="string")
     * @ORM\GeneratedValue(strategy="NONE")
     */
    public string $tabId;
    /**
     * @ORM\Column(type="integer")
     */
    public int $tableNumber;
    /**
     * @ORM\Column(type="string")
     */
    public string $waiter;
    /** @var array<TabItem> */
    public array $toServe;
    /** @var array<TabItem> */
    public array $inPreparation;
    /** @var array<TabItem> */
    public array $served;

    /**
     * @ORM\OneToMany(cascade={"persist"}, targetEntity="TabItem", mappedBy="tab")
     * @var Collection
     */
    public Collection $items;

    public function __construct(string $tabId, int $tableNumber, string $waiter, array $toServe, array $inPreparation, array $served)
    {
        $this->tabId = $tabId;
        $this->tableNumber = $tableNumber;
        $this->waiter = $waiter;
        $this->toServe = $toServe;
        $this->inPreparation = $inPreparation;
        $this->served = $served;
        $this->items = new ArrayCollection();
    }

    public function moveItems(array $menuNumbers, \Closure $from, \Closure $to) : void
    {
        $fromList = $from($this);
    }
}