<?php

namespace ClansInstitution\Commands;

use ClansInstitution\Main;
use ClansInstitution\UI\CommandsUI\InvitationUI;
use ClansInstitution\Utils\SearchUtils;
use ClansInstitution\Utils\StoreUtils;
use ClansInstitution\Utils\Utils;
use pocketmine\player\Player;
use pocketmine\world\sound\ItemBreakSound;

class LeaveClan{
    
    public const OWNER_LEAVE_TRY = "\n\n§cYou can't leave the clan becаuse you're the owner. You must transfer leadership or dissolve the clan.\n\n";

    public static function OnLeave(Player $player, array $args){
        
        if(self::OnValidate($player, $args)){
            $nick = $player->getName();

            Utils::dropMember($nick);
            SearchUtils::isClanStaff($nick) && Utils::dropStaffMember($nick);
            
            $clan_name = SearchUtils::getClanName($nick);
            $clan_data = SearchUtils::getClanData($clan_name);
            
            StoreUtils::OnStoreJSON($clan_data, $clan_name); //aplicar cambios en JSON
            Utils::dropAssociatedClan($nick);
            Utils::sendClanMessage("⨱ §d$nick §c hаs left your clan §f⩟", $clan_name);
            $player->sendMessage("⩌§f You have left the §d$clan_name §fclаn");
            $player->getWorld()->addSound($player->getPosition()->asVector3(), new ItemBreakSound, [$player]);        
        }
    }

    protected static function OnValidate(Player $player, array $args):bool{
        
        if(count($args) > 1){ 
            $player->sendMessage("§f Usаge §e/clаn leave"); 
            return false;
        }

        if(!SearchUtils::IsAssociatedToClan($player->getName())){
            InvitationUI::OnSendFailForm($player, ViewMembers::CLAN_NOT_FOUND);
            return false;
        }

        if(SearchUtils::isClanOwner($player->getName())){
            InvitationUI::OnSendFailForm($player, self::OWNER_LEAVE_TRY);
            return false;
        }
        
        return true;
    }

}
