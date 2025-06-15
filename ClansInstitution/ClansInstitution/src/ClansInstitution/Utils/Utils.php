<?php 

namespace ClansInstitution\Utils;

use ClansInstitution\Main;

class Utils {
                                  
    public static function OnAssociateToClan(string $player_name, string $clan_name):void{
        array_push(Main::$clan_list[$clan_name]["members"],$player_name);
        self::OnMoveToAssosiatedClan($player_name,$clan_name);
        StoreUtils::OnStoreJSON(SearchUtils::getClanData($clan_name),$clan_name);
    } 

    public static function OnMoveToAssosiatedClan(string $player_name, string $clan_name):void{
        Main::$associated_clan[$player_name] = $clan_name; //store the player in assosiated clans array
    }

    public static function dropAssociatedClan(string $player_name):void{
        unset(Main::$associated_clan[$player_name]);
    }

    public static function dropMember(string $player_name):void{
        $clan_name = SearchUtils::getClanName($player_name);
        $member_key = array_search($player_name,Main::$clan_list[$clan_name]["members"]); //this will return where is the player exactly inside of array
        unset(Main::$clan_list[$clan_name]["members"][$member_key]); //remove member
    }

    public static function dropStaffMember(string $player_name):void{
        $clan_name = SearchUtils::getClanName($player_name);
        $member_key = array_search($player_name,Main::$clan_list[$clan_name]["staff"]); //this will return where is the player exactly inside of array
        unset(Main::$clan_list[$clan_name]["staff"][$member_key]); //remove member
    }

    public static function getOnlinePlayersList():array{
        $list = [];
        foreach (Main::getInstance()->getServer()->getOnlinePlayers() as $player) {
            $list[] = $player->getName();
        } return $list;
    }


    /**
     * Esta funcion tiene el proposito de eliminar de mandar un clan a la verga.
     * 
     * @return void
     */
    public static function onDemolishClan(string $clan_name): void {

        $path = Main::getInstance()->getDataFolder()."clans/". strtolower($clan_name) . ".json";
        unset(Main::$clan_list[$clan_name]);

        foreach (Main::$associated_clan as $member => $clan) {
            if($clan === $clan_name) unset(Main::$associated_clan[$member]);
        }

        if(file_exists($path)){
            unlink($path);
        }  
    }

    public static function sendClanMessage(string $message, string $clan_name):void{
        
        $players = Main::getInstance()->getServer()->getOnlinePlayers();

        foreach ($players as $player) {
            if(SearchUtils::IsAssociatedToClan($player->getName()) && SearchUtils::getClanName($player->getName()) === $clan_name){
                $player->sendMessage($message);
            }
        }
    }
}
