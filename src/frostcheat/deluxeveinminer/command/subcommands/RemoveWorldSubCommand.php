<?php

namespace frostcheat\deluxeveinminer\command\subcommands;

use CortexPE\Commando\BaseSubCommand;
use frostcheat\deluxeveinminer\command\args\WorldArgument;
use frostcheat\deluxeveinminer\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class RemoveWorldSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("removeworld", "Remove a world from the world blacklist");
        $this->setPermission("deluxeveinminer.command.remove.world");
    }

    public function prepare(): void {
        $this->registerArgument(0, new WorldArgument("world"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $worldName = str_replace("_"," ", strtolower($args["world"]->getFolderName()));

        if (!in_array($worldName, Loader::getInstance()->worlds)) {
            $sender->sendMessage(TextFormat::colorize("&cThis world is not on the blacklist."));
            return;
        }

        $index = array_search($worldName, Loader::getInstance()->worlds, true);
        if ($index !== false) {
            unset(Loader::getInstance()->worlds[$index]);
            Loader::getInstance()->worlds = array_values(Loader::getInstance()->worlds);
            Loader::getInstance()->save();
            $sender->sendMessage(TextFormat::colorize("&aThe block &e$worldName&a has been successfully removed from the blacklist."));
        } else {
            $sender->sendMessage(TextFormat::colorize("&cUnexpected error: block not found in blacklist."));
        }
    }
}