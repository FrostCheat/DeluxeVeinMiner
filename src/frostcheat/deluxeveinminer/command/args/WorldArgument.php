<?php

namespace frostcheat\deluxeveinminer\command\args;

use CortexPE\Commando\args\StringEnumArgument;
use frostcheat\deluxeveinminer\Loader;
use pocketmine\command\CommandSender;
use pocketmine\world\World;

class WorldArgument extends StringEnumArgument {

    public function getTypeName(): string {
        return "world";
    }

    public function canParse(string $testString, CommandSender $sender): bool {
        return $this->getValue($testString) instanceof World;
    }

    public function parse(string $argument, CommandSender $sender): ?World {
        return $this->getValue($argument);
    }

    public function getValue(string $string): ?World {
        foreach (Loader::getInstance()->getServer()->getWorldManager()->getWorlds() as $world) {
            if (strtolower($world->getFolderName()) === $string) {
                return $world;
            }
        }
        return null;
    }

    public function getEnumValues(): array {
        return array_map(
            fn(World $world) => strtolower($world->getFolderName()),
            Loader::getInstance()->getServer()->getWorldManager()->getWorlds()
        );
    }
}
