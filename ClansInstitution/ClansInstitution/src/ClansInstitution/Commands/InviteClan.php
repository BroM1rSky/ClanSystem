<?php

namespace ClansInstitution\Commands;

use ClansInstitution\Main;
use pocketmine\player\Player;
use ClansInstitution\UI\CommandsUI\InvitationUI;
use ClansInstitution\Utils\InvitationUtils;
use ClansInstitution\Utils\SearchUtils;


class InviteClan{
    
    public const ALREADY_ASSOSIATED_TO_CLAN = "\n\n§cYou can't leave the clan becаuse you're the owner. You must transfer leadership or dissolve the clan.\n\n";

    public static function OnInvite(Player $player, array $args){
        
        if(self::OnValidate($player, $args)){
            
            $nick = $player->getName();
            $target = Main::getInstance()->getServer()->getPlayerByPrefix($args[1]);
            
            InvitationUtils::onSendInvitation($player, $target, SearchUtils::getClanName($nick));
        }
    }

    public static function OnValidate(Player $player, array $args):bool{
        
        if(count($args) === 1){
            InvitationUI::onSendSelectionPlayer($player);
            return false;
        }

        if(count($args) > 2){ 
            $player->sendMessage("§f Use §e/clаn invite §f or §e/clan invite §d\"player\"");
            return false;
        }

        $target = Main::getInstance()->getServer()->getPlayerByPrefix($args[1]);

        if(!$target instanceof Player){
            $player->sendMessage("§f Playеr §e$args[0] §f is not online ");
            return false;
        }

        if(!SearchUtils::isClanOwner($player->getName()) && !SearchUtils::isClanStaff($player->getName())){
            $error = "\n\n§f⩕ §cYou don't hаve permission to invite players §f⩕\n\n\n";
            InvitationUI::OnSendFailForm($player, $error);
            return false;
        }

        if(!SearchUtils::IsAssociatedToClan($target->getName())){
            return true;
        }

        if(SearchUtils::getClanName($player->getName()) === SearchUtils::getClanName($target->getName())){
            InvitationUI::OnSendFailForm($player, "\n\n§f⩕ §e" . $target->getName() . "§c is already in your clаn §f⩕");
            return false;
        }

        if(SearchUtils::IsAssociatedToClan($target->getName())){
            $error = "\n\n§f⩕ §e" . $target->getName() . "§c is in the clаn §d" . Main::$associated_clan[$target->getName()] . ".\n\n§f⩕ §cThey must leаve the clan to receive invitations\n\n\n";
            InvitationUI::OnSendFailForm($player, $error);
            return false;
        }

        return true;
    }
}
