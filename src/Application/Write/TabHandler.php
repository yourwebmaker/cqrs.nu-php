<?php

declare(strict_types=1);

namespace Cafe\Application\Write;

use Cafe\Application\Read\OpenTabsQueries;
use Cafe\Domain\Tab\TabId;
use Cafe\Domain\Tab\TabRepository;
use Cafe\UserInterface\Web\StaticData\MenuItem;
use Cafe\UserInterface\Web\StaticData\StaticData;

class TabHandler
{
    private TabRepository $repository;
    private OpenTabsQueries $queries;

    public function __construct(TabRepository $repository, OpenTabsQueries $queries)
    {
        $this->repository = $repository;
        $this->queries = $queries;
    }

    public function markServed(MarkItemsServedCommand $command) : void
    {
        $tabId = TabId::fromString($command->tabId);
        $tab = $this->repository->get($tabId);
        $menu = StaticData::getMenu();

        $drinksNumbers = [];
        $foodNumbers = [];

        /**
         * @var int $menuNumber
         * @var MenuItem $menuItem
         */
        foreach ($command->menuNumbers as $menuNumber) {
            if ($menu[$menuNumber]->isDrink) {
                $drinksNumbers[] = $menuNumber;
            } else {
                $foodNumbers[] = $menuNumber;
            }
        }

        if (count($drinksNumbers) > 0) {
            $tab->markDrinksServed(new MarkDrinksServed($command->tabId, $drinksNumbers));
        }

//        if ($food) {
//            $tab->markFoodServed($food);
//        }

        $this->repository->save($tab);
    }
}