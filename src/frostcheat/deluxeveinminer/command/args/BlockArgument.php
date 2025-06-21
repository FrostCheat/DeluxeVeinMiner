<?php

namespace frostcheat\deluxeveinminer\command\args;

use CortexPE\Commando\args\StringEnumArgument;
use frostcheat\deluxeveinminer\Loader;
use pocketmine\block\Block;
use pocketmine\command\CommandSender;

class BlockArgument extends StringEnumArgument {
    public function getTypeName(): string {
        return "ore";
    }

    public function canParse(string $testString, CommandSender $sender): bool {
        return $this->getValue($testString) instanceof Block;
    }

    public function parse(string $argument, CommandSender $sender): ?Block {
        return $this->getValue($argument);
    }

    public function getValue(string $string): ?Block {
        foreach (Loader::getInstance()->getOres() as $ore) {
            if (str_replace(" ", "_", strtolower($ore->getName())) === strtolower($string)) {
                return $ore;
            }
        }
        return null;
    }

    public function getEnumValues(): array {
        return array_map(fn(Block $block) => str_replace(" ", "_", strtolower($block->getName())), Loader::getInstance()->getOres());
    }
}