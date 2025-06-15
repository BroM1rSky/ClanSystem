<?php 

namespace ClansInstitution\Utils;

use ClansInstitution\Main;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class StuffUtils {
                                  
    public static function onGrandStuff(string $player_name, string $clan_name):void{
        array_push(Main::$clan_list[$clan_name]["staff"],$player_name);
        StoreUtils::OnStoreJSON(SearchUtils::getClanData($clan_name),$clan_name);
    } 

    public static function onRefuseStuff(string $player_name):void{
        $clan_name = SearchUtils::getClanName($player_name);
        $member_key = array_search($player_name,Main::$clan_list[$clan_name]["staff"]); //this will return where is the player exactly inside of array
        unset(Main::$clan_list[$clan_name]["staff"][$member_key]); //remove member
    }

    public static function onTransferOwnership(string $player_name, string $clan_name):void{
        
        Main::$clan_list[$clan_name]["owner"] = $player_name;

        if(SearchUtils::isClanStaff($player_name)){
            self::onRefuseStuff($player_name);
        }

        StoreUtils::OnStoreJSON(SearchUtils::getClanData($clan_name),$clan_name);
    }
}
