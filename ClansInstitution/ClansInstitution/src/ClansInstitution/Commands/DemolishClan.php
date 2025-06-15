<?php

namespace ClansInstitution\Commands;

use ClansInstitution\Main;
use ClansInstitution\UI\CommandsUI\InvitationUI;
use ClansInstitution\Utils\SearchUtils;
use ClansInstitution\Utils\StoreUtils;
use ClansInstitution\Utils\Utils;
use pocketmine\player\Player;

class DemolishClan {
    
    public const MEMBER_CLAN_REMOVE_ATTEMPT = "\n\n§cYоu can't remove this clan because you're not the owner. Nice try §e:) \n\n";

    public static function onExecute(Player $player, array $args = []){
        if(self::onUsage($player,$args)){
            InvitationUI::onSendConfirmationForm($player);
        }
    }

    public static function onCommit(Player $player, mixed $data){

        if($data !== NULL || $data !== 0){

            $nick = $player->getName(); $clan_name = SearchUtils::getClanName($nick);   
            
            self::dropOwner($nick);
            Utils::onDemolishClan($clan_name);        
            Utils::sendClanMessage("⨲ §d$nick §c dissоlved your clan §f⨠ ⨳ ⨠ ", $clan_name);        
            $player->sendMessage("⨲ §fYou've dissolved the clаn §e$clan_name §f⨠ ⨳ ⨠" );
        }
    }

    /**
     * @warning -> No uses esta funcion en ningin otro sitio.
     *
     * @param string $owner
     * @return void
     */
    private static function dropOwner(string $owner){
        $clan_name = SearchUtils::getClanName($owner);
        unset(Main::$clan_list[$clan_name]["owner"]);
        unset(Main::$associated_clan[$owner]);

    }

    protected static function onUsage(Player $player, array $args):bool{

        if(!SearchUtils::IsAssociatedToClan($player->getName())){
            InvitationUI::OnSendFailForm($player,ViewMembers::CLAN_NOT_FOUND);
            return false;
        }

        if(!SearchUtils::isClanOwner($player->getName())){
            InvitationUI::OnSendFailForm($player,self::MEMBER_CLAN_REMOVE_ATTEMPT);
            return false;
        }
        
        return true;
    }
   
}