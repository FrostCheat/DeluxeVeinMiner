<?php

namespace frostcheat\deluxeveinminer\command;

use CortexPE\Commando\BaseCommand;
use frostcheat\deluxeveinminer\command\subcommands\AddBlockSubCommand;
use frostcheat\deluxeveinminer\command\subcommands\AddWorldSubCommand;
use frostcheat\deluxeveinminer\command\subcommands\HelpSubCommand;
use frostcheat\deluxeveinminer\command\subcommands\ReloadSubCommand;
use frostcheat\deluxeveinminer\command\subcommands\RemoveBlockSubCommand;
use frostcheat\deluxeveinminer\command\subcommands\RemoveWorldSubCommand;
use pocketmine\command\CommandSender;
use pocketmine\plugin\Plugin;
use pocketmine\utils\TextFormat;

class VeinMinerCommand extends BaseCommand {

    public function __construct(Plugin $plugin) {
        parent::__construct($plugin, "deluxeveinminer", "", ["dvm", "veinminer", "veinm", "deluxevm"]);
        $this->setPermission("deluxeveinminer.command");
    }

    public function prepare(): void {
        $this->registerSubCommand(new AddBlockSubCommand());
        $this->registerSubCommand(new AddWorldSubCommand());
        $this->registerSubCommand(new ReloadSubCommand());
        $this->registerSubCommand(new RemoveBlockSubCommand());
        $this->registerSubCommand(new RemoveWorldSubCommand());
        $this->registerSubCommand(new HelpSubCommand($this->getSubCommands()));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $sender->sendMessage(TextFormat::colorize("&cUse /$aliasUsed help"));
    }
}