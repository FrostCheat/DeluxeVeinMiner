<?php

namespace frostcheat\deluxeveinminer\command\subcommands;

use CortexPE\Commando\BaseSubCommand;
use frostcheat\deluxeveinminer\command\args\WorldArgument;
use frostcheat\deluxeveinminer\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class AddWorldSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("addworld", "Add a world to the world blacklist");
        $this->setPermission("deluxeveinminer.command.add.world");
    }

    public function prepare(): void {
        $this->registerArgument(0, new WorldArgument("world"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $worldName = str_replace("_"," ", strtolower($args["world"]->getFolderName()));

        if (in_array($worldName, Loader::getInstance()->worlds)) {
            $sender->sendMessage(TextFormat::colorize("&cThis world is already blacklisted."));
            return;
        }

        Loader::getInstance()->worlds[] = $worldName;
        Loader::getInstance()->save();

        $sender->sendMessage(TextFormat::colorize("&aThe world &e$worldName&a has been successfully added to the blacklist."));
    }
}