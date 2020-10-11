<?php

declare(strict_types=1);

namespace Cafe\Application\Read\OpenTabs;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="read_model_tab_item")
 */
class TabItem
{
    /**
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="integer")
     */
    public string $id;
    /**
     * @ORM\ManyToOne(targetEntity="Tab", inversedBy="items")
     */
    public Tab $tab;
    /**
     * @ORM\Column(type="integer")
     */
    public int $menuNumber;
    /**
     * @ORM\Column(type="string")
     */
    public string $description;
    /**
     * @ORM\Column(type="float")
     */
    public float $price;
    /**
     * @ORM\Column(type="string")
     */
    private string $status;
}