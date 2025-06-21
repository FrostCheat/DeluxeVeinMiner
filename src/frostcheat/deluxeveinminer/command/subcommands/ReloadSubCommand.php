<?php

namespace frostcheat\deluxeveinminer\command\subcommands;

use CortexPE\Commando\BaseSubCommand;
use frostcheat\deluxeveinminer\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class ReloadSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("reload", "Reload the plugin configuration");
        $this->setPermission("deluxeveinminer.command.reload");
    }

    public function prepare(): void {}

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        Loader::getInstance()->reloadConfig();
        Loader::getInstance()->load();
        $sender->sendMessage(TextFormat::colorize("&aThe plugin configuration has been reloaded successfully."));
    }
}