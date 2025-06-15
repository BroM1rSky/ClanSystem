<?php 

namespace ClansInstitution\Utils;

use ClansInstitution\Main;
use pocketmine\utils\Config;

class StoreUtils {

    public static function onMergeTempData(string $clan_name){
        self::onMigrateData($clan_name); //this func are moving the temp data to original array with persistent data and store it in JSON.  
        self::deleteTempData($clan_name); //in the end, we need to delete the temp data.
    }

    protected static function deleteTempData($clan_name):void{
        unset(Main::$temp_clans[$clan_name]); 
    }

    protected static function onMigrateData(string $clan_name):void{
        $clan_to_move = Main::$temp_clans[$clan_name]; 
        Main::$clan_list[$clan_name] = $clan_to_move;
        Utils::OnMoveToAssosiatedClan($clan_to_move["owner"],$clan_name);
        self::OnStoreJSON($clan_to_move,$clan_name);
    }

    public static function OnStoreJSON(array $clan_to_move, string $clan_name):void{
        //here we are creating a dedicated json file for the new clan
        copy("plugin_data/ClansInstitution/clans/template.json","plugin_data/ClansInstitution/clans/".strtolower($clan_name).".json");

        $clan_config = new Config("plugin_data/ClansInstitution/clans/".strtolower($clan_name).".json",Config::JSON);
        $clan_config->set("clan_name",$clan_name); //store clan name
        $clan_config->set("info",$clan_to_move);  //we are storing the clan info into dedicated JSON file.
        $clan_config->save();
    }


    public static function storeTempClanName(string $clan_name){
        Main::$temp_clans[$clan_name]["name"] = $clan_name;
    }

    public static function updateClanName(string $clan_name, string $new_name):void{
        
        Main::$clan_list[$clan_name]["name"] = $new_name;
        Main::$clan_list[$new_name] = Main::$clan_list[$clan_name];
        unset(Main::$clan_list[$clan_name]);
        
        //var_dump(Main::$clan_list[$new_name]);

        foreach (Main::$associated_clan as $nick_player => $clan){
            if ($clan === $clan_name) Main::$associated_clan[$nick_player] = $new_name;
        }   rename("plugin_data/ClansInstitution/clans/".strtolower($clan_name).".json" ,"plugin_data/ClansInstitution/clans/".strtolower($new_name).".json");

        self::OnStoreJSON(SearchUtils::getClanData($new_name),$new_name);
    }

    public static function updateClanDescription(string $clan_name, string $clan_desc):void{
        Main::$clan_list[$clan_name]["desc"] = $clan_desc;
        self::OnStoreJSON(SearchUtils::getClanData($clan_name),$clan_name);
    }

    public static function storeTempDescription(string $clan_name, string $clan_desc){
        Main::$temp_clans[$clan_name]["desc"] = $clan_desc;
    }

    public static function storeTempPublicDetails(string $clan_name, int $is_public){
        $is_public === 1 ? Main::$temp_clans[$clan_name]["is_public"] = true : Main::$temp_clans[$clan_name]["is_public"] = false;
    }

    public static function storeTempOwner(string $clan_name, string $clan_owner){
        Main::$temp_clans[$clan_name]["owner"] = $clan_owner;
        Main::$temp_clans[$clan_name]["staff"] = [$clan_owner]; // addtionally this will store this as a template
        Main::$temp_clans[$clan_name]["members"] = [$clan_owner]; // addtionally this will store this as a template
    }

    

}