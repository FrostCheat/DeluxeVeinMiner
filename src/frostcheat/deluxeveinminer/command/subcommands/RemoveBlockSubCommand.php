<?php

namespace frostcheat\deluxeveinminer\command\subcommands;

use CortexPE\Commando\BaseSubCommand;
use frostcheat\deluxeveinminer\command\args\BlockArgument;
use frostcheat\deluxeveinminer\Loader;
use pocketmine\command\CommandSender;
use pocketmine\utils\TextFormat;

class RemoveBlockSubCommand extends BaseSubCommand {

    public function __construct() {
        parent::__construct("removeblock", "Remove a block from the block blacklist");
        $this->setPermission("deluxeveinminer.command.remove.block");
    }

    public function prepare(): void {
        $this->registerArgument(0, new BlockArgument("block"));
    }

    public function onRun(CommandSender $sender, string $aliasUsed, array $args): void {
        $blockName = str_replace("_"," ", strtolower($args["block"]->getName()));

        if (!in_array($blockName, Loader::getInstance()->blacklistBlocks)) {
            $sender->sendMessage(TextFormat::colorize("&cThis block is not on the blacklist."));
            return;
        }

        $index = array_search($blockName, Loader::getInstance()->blacklistBlocks, true);
        if ($index !== false) {
            unset(Loader::getInstance()->blacklistBlocks[$index]);
            Loader::getInstance()->blacklistBlocks = array_values(Loader::getInstance()->blacklistBlocks); // Reindexar el array
            Loader::getInstance()->save();
            $sender->sendMessage(TextFormat::colorize("&aThe block &e$blockName&a has been successfully removed from the blacklist."));
        } else {
            $sender->sendMessage(TextFormat::colorize("&cUnexpected error: block not found in blacklist."));
        }
    }
}