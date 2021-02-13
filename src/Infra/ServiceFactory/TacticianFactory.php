<?php

declare(strict_types=1);

namespace Cafe\Infra\ServiceFactory;

use Cafe\Application\Write\TabHandler;
use League\Tactician\CommandBus;
use League\Tactician\Handler\CommandHandlerMiddleware;
use League\Tactician\Handler\CommandNameExtractor\ClassNameExtractor;
use League\Tactician\Handler\Locator\HandlerLocator;
use League\Tactician\Handler\MethodNameInflector\HandleClassNameInflector;

class TacticianFactory
{
    private TabHandler $handler;

    public function __construct(TabHandler $handler)
    {
        $this->handler = $handler;
    }

    public function create(): CommandBus
    {
        $handlerMiddleware = new CommandHandlerMiddleware(
            new ClassNameExtractor(),
            new class ($this->handler) implements HandlerLocator
            {
                private TabHandler $handler;

                public function __construct(TabHandler $handler)
                {
                    $this->handler = $handler;
                }

                public function getHandlerForCommand($commandName)
                {
                    return $this->handler;
                }
            },
            new HandleClassNameInflector(),
        );

        return new CommandBus([$handlerMiddleware]);
    }
}
