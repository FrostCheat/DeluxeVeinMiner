<?php

namespace frostcheat\deluxeveinminer;

use pocketmine\block\Block;
use pocketmine\block\VanillaBlocks;
use pocketmine\event\block\BlockBreakEvent;
use pocketmine\event\Listener;
use pocketmine\item\Durable;
use pocketmine\item\enchantment\VanillaEnchantments;
use pocketmine\item\Item;
use pocketmine\item\VanillaItems;
use pocketmine\math\Vector3;
use pocketmine\player\Player;
use pocketmine\world\particle\BlockBreakParticle;
use pocketmine\world\Position;

class EventListener implements Listener {

    private const MAX_BLOCKS = 64;

    public function onBlockBreak(BlockBreakEvent $event): void {
        $player = $event->getPlayer();
        $block = $event->getBlock();
        $world = $block->getPosition()->getWorld();

        if (in_array($world->getFolderName(), Loader::getInstance()->worlds)) return;

        foreach (Loader::getInstance()->blacklistBlocks as $blacklistedName) {
            if (str_contains(strtolower($block->getName()), strtolower($blacklistedName))) return;
        }

        if (!$this->isVeinMinerBlock($block)) return;

        $item = $player->getInventory()->getItemInHand();
        if (!$item instanceof Item || !$block->getBreakInfo()->isToolCompatible($item)) return;

        $visited = [];
        $origin = $block->getPosition()->asVector3();
        $visited["{$origin->x}:{$origin->y}:{$origin->z}"] = true;

        $this->veinMine($block, $item, $player, $visited, $origin);
    }

    public function veinMine(Block $block, Item $item, Player $player, array &$visited = [], ?Vector3 $origin = null): void {
        if (count($visited) >= self::MAX_BLOCKS) return;

        $pos = $block->getPosition();
        $vec = $pos->asVector3();
        $origin ??= $vec;

        $world = $pos->getWorld();
        $blockId = $block->getTypeId();

        for ($x = -1; $x <= 1; $x++) {
            for ($y = -1; $y <= 1; $y++) {
                for ($z = -1; $z <= 1; $z++) {
                    if ($x === 0 && $y === 0 && $z === 0) continue;

                    $neighborVec = $vec->add($x, $y, $z);
                    $key = "{$neighborVec->x}:{$neighborVec->y}:{$neighborVec->z}";
                    if (isset($visited[$key])) continue;

                    $neighborPos = Position::fromObject($neighborVec, $world);
                    $neighborBlock = $world->getBlock($neighborPos);

                    if ($neighborBlock->getTypeId() === $blockId) {
                        $visited[$key] = true;

                        $world->setBlock($neighborPos, VanillaBlocks::AIR());
                        $world->addParticle($neighborPos, new BlockBreakParticle($neighborBlock));

                        foreach ($neighborBlock->getDrops($item) as $drop) {
                            $world->dropItem($origin, $drop);
                        }

                        if ($item instanceof Durable) {
                            $chance = 100 / ($item->getEnchantmentLevel(VanillaEnchantments::UNBREAKING()) + 1);
                            if (mt_rand(1, 100) <= $chance) {
                                $item->applyDamage(1);
                            }
                            if ($item->getDamage() >= $item->getMaxDurability()) {
                                $item = VanillaItems::AIR();
                            }
                            $player->getInventory()->setItemInHand($item);
                        }

                        $this->veinMine($neighborBlock, $item, $player, $visited, $origin);
                    }
                }
            }
        }
    }

    private function isVeinMinerBlock(Block $block): bool {
        $oreIds = array_map(fn(Block $b) => $b->getTypeId(), Loader::getInstance()->getOres());
        return in_array($block->getTypeId(), $oreIds, true);
    }
}
