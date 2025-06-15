<?php

namespace ClansInstitution\Commands;

use ClansInstitution\Main;
use ClansInstitution\UI\CommandsUI\InvitationUI;
use ClansInstitution\UI\CommandsUI\ViewMembersUI;
use ClansInstitution\UI\Managment\ManageClan;
use ClansInstitution\Utils\SearchUtils;
use pocketmine\player\Player;


class ViewMembers{

    public const CLAN_NOT_FOUND = "\n\n      §f⩕ §cYоu haven't joined any clan yet §f⩕\n\n";
    
    public static function OnView(Player $player, array $args){
        
        if(self::OnValidate($player, $args)){
            
            count($args) == 2 ? ViewMembersUI::OnSendMemberForm($player, $args[1]) : ViewMembersUI::OnSendMemberForm($player, SearchUtils::getClanName($player->getName()));
            
        }
    }

    protected static function OnValidate(Player $player, array $args):bool{
        
        if(count($args) > 2){ 
            $player->sendMessage("§f Usе §e/clan members \n§f or §e/clan members §d\"clan\"");
            return false;
        }
        
        if(count($args) === 1 && !SearchUtils::IsAssociatedToClan($player->getName())){
            InvitationUI::OnSendFailForm($player, self::CLAN_NOT_FOUND);
            return false;
        }

        if(count($args) === 2 && !SearchUtils::ClanAlreadyExists($args[1])){
            $player->sendMessage("§f Thе clan §d$args[1]§f does not exist ");
            return false; 
        }
        
        return true;
    }
}
