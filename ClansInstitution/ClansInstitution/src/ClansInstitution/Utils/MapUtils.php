<?php 

namespace ClansInstitution\Utils;

use ClansInstitution\Main;
use pocketmine\utils\Config;

class MapUtils {

    /**
     * Esta funcion es la que lee todos los JSON's que son los clanes, y los almacena en un array.
     * Al final, tendras un array $clan_list que tendra la siguiente estructura:
     * 
     * clan_list = [
     *      "pitoGanG" => [
     *          name => pitoGanG
     *          desc => ...
     *          members => ["member1", "member2" ...]
     *          ... etc ...
     *      ]
     * ]-*
     * 
     * @param Main $main
     * @return void
     */
    public static function fetchDataToArray(Main $main):void{

        $clan_files = scandir($main->getDataFolder()."clans/"); //get all files inside of the clans folder
        $clan_files = array_diff($clan_files,["template.json", ".", ".."]); //apply filter to clean NOT clan files
        sort($clan_files);

        foreach ($clan_files as $clan_file) {            
            $clan_data = new Config($main->getDataFolder()."clans/$clan_file",Config::JSON);
            Main::$clan_list[$clan_data->get("clan_name")] = $clan_data->get("info"); //this will add JSON of current clan data to clan_list array
            //this means $clan_list["PitoGanG"] => [name,desc,owner,staff,members ... etc]
        }
    }
    
    /**
     * Esta funcion tiene el proposito de crear un array de jugadores (nombres) y su clan asosiado
     * Esto sirve para obtener el clan asociado a un jugador de manera inmediata y eficiente
     * 
     * Obtendras el siguente array al final:
     * 
     * associated_clan = [ 
     *      "ZVEZDA2016" => "PitoGanG",
     *      "OPT1MUS7927" => "DeaDGriF"
     * ]
     *
     * (En la documentacion de vanguard tambien hay info sobre el array asociated_clan)
     * 
     * @return void
     */
    public static function fetchAssociations():void{
        foreach (Main::$clan_list as $clan) {
            foreach ($clan["members"] as $member) {
                Main::$associated_clan[$member] = $clan["name"];
            }
        }
    }

}