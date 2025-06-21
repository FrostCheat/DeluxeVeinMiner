<?php

namespace frostcheat\deluxeveinminer\command\subcommands;

use CortexPE\Commando\BaseSubCommand;
use frostcheat\deluxeveinminer\command\args\BlockArgument;
use frostcheat\deluxeveinminer\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class AddBlockSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("addblock", "Add a block to the block blacklist");
        $this->setPermission("deluxeveinminer.command.add.block");
    }

    public function prepare(): void {
        $this->registerArgument(0, new BlockArgument("block"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $blockName = str_replace("_"," ", strtolower($args["block"]->getName()));

        if (in_array($blockName, Loader::getInstance()->blacklistBlocks)) {
            $sender->sendMessage(TextFormat::colorize("&cThis block is already blacklisted."));
            return;
        }

        Loader::getInstance()->blacklistBlocks[] = $blockName;
        Loader::getInstance()->save();

        $sender->sendMessage(TextFormat::colorize("&aThe block &e$blockName&a has been successfully added to the blacklist."));
    }
}