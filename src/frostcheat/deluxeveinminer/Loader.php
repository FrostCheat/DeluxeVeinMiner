<?php

declare(strict_types=1);

namespace frostcheat\deluxeveinminer;

use CortexPE\Commando\PacketHooker;
use frostcheat\deluxeveinminer\command\VeinMinerCommand;
use JackMD\ConfigUpdater\ConfigUpdater;
use JackMD\UpdateNotifier\UpdateNotifier;
use pocketmine\block\VanillaBlocks;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\SingletonTrait;

class Loader extends PluginBase {
    use SingletonTrait;

    public const CONFIG_VERSION = 1;
    public array $worlds;
    public array $blacklistBlocks;

    public function onLoad(): void {
        self::setInstance($this);
    }

    public function onEnable(): void {
        UpdateNotifier::checkUpdate($this->getDescription()->getName(), $this->getDescription()->getVersion());
        if (ConfigUpdater::checkUpdate($this, $this->getConfig(), "config-version", self::CONFIG_VERSION)) {
            $this->reloadConfig();
        }

        if (!PacketHooker::isRegistered())
            PacketHooker::register($this);

        $this->load();

        $this->getServer()->getCommandMap()->register("deluxeveinminer", new VeinMinerCommand($this));
        $this->getServer()->getPluginManager()->registerEvents(new EventListener(), $this);
    }

    public function load(): void {
        $this->worlds = [];
        $this->blacklistBlocks = [];

        $config = $this->getConfig();
        foreach($config->get("blacklisted-ores", []) as $block) {
            $this->blacklistBlocks[] = $block;
        }

        foreach($config->get("blacklisted-worlds", []) as $world) {
            $this->worlds[] = $world;
        }
    }

    public function save(): void {
        $config = $this->getConfig();

        $config->set("blacklisted-worlds", $this->worlds);
        $config->set("blacklisted-ores", $this->blacklistBlocks);

        $config->save();
    }

    public function getOres(): array {
        return [
            VanillaBlocks::COAL_ORE(),
            VanillaBlocks::IRON_ORE(),
            VanillaBlocks::GOLD_ORE(),
            VanillaBlocks::DIAMOND_ORE(),
            VanillaBlocks::EMERALD_ORE(),
            VanillaBlocks::COPPER_ORE(),
            VanillaBlocks::REDSTONE_ORE(),
            VanillaBlocks::LAPIS_LAZULI_ORE(),
            VanillaBlocks::DEEPSLATE_COAL_ORE(),
            VanillaBlocks::DEEPSLATE_IRON_ORE(),
            VanillaBlocks::DEEPSLATE_GOLD_ORE(),
            VanillaBlocks::DEEPSLATE_DIAMOND_ORE(),
            VanillaBlocks::DEEPSLATE_EMERALD_ORE(),
            VanillaBlocks::DEEPSLATE_COPPER_ORE(),
            VanillaBlocks::DEEPSLATE_REDSTONE_ORE(),
            VanillaBlocks::DEEPSLATE_LAPIS_LAZULI_ORE(),
            VanillaBlocks::NETHER_QUARTZ_ORE(),
            VanillaBlocks::NETHER_GOLD_ORE(),
        ];
    }
}
