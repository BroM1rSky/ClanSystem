<?php 

namespace ClansInstitution\Utils;

use ClansInstitution\Main;
use pocketmine\utils\Config;

class SearchUtils {

    public static function isAssociatedToClan(string $player_name): bool {
        return isset(Main::$associated_clan[$player_name]);
    }

    public static function getClanName(string $player_name): string {
        return Main::$associated_clan[$player_name];
    }

    /**
     * Esta función devuelve el nombre original del clan al cual se quiere 
     * consultar información. Esto puede ser muy útil en casos donde el nombre debe representarse
     * de manera original.
     *
     * @param string $raw_clan_name
     * @return string
     */
    public static function getRealClanName(string $raw_clan_name): string {
        if (self::clanAlreadyExists($raw_clan_name)) {
            $config = new Config("plugin_data/ClansInstitution/clans/" . strtolower($raw_clan_name) . ".json", Config::JSON);
            return $config->get("clan_name");
        }
        return "";
    }

    public static function getClanData(string $clan_name): array {
        return Main::$clan_list[$clan_name];
    }

    public static function isClanOwner(string $player_name): bool {
        $clan_name = Main::$associated_clan[$player_name];
        return Main::$clan_list[$clan_name]["owner"] === $player_name;
    }

    public static function isClanStaff(string $player_name): bool {
        $clan_name = Main::$associated_clan[$player_name];
        return in_array($player_name, Main::$clan_list[$clan_name]["staff"]);
    }

    /**
     * Obtener el array de miembros de un clan.
     * Pero asegúrate de que el clan exista.
     *
     * @param string $clan_name
     * @return array
     */
    public static function getClanMembersArray(string $clan_name): array { 
        $real_name = self::getRealClanName($clan_name); 
        return array_values(Main::$clan_list[$real_name]["members"]);
    }

    /**
     * Obtener el array de miembros que son Staff de un clan.
     * Pero asegúrate de que el clan exista.
     *
     * @param string $clan_name
     * @return array
     */
    public static function getClanStaffMembers(string $clan_name): array {
        $real_name = self::getRealClanName($clan_name); 
        return array_values(Main::$clan_list[$real_name]["staff"]); 
    }

    public static function getClanMembers(string $clan_name): string {
        
        if (!self::clanAlreadyExists($clan_name)) return "§cThis clan does not еxist!";
        
        $real_name = self::getRealClanName($clan_name); // esto es necesario, ya que el nombre del clan debe coincidir exactamente para poder encontrarlo en el array estático. Y el nombre que se pasa a esta función puede estar en minúsculas o en diferentes formatos
        
        $clan = Main::$clan_list[$real_name];      
        $clan_members = "";

        foreach ($clan["members"] as $member) {
            
            if (self::isClanOwner($member)) {
                $clan_members .= " §f$member §r§f(§eOwnеr§f )\n\n";
            } elseif (self::isClanStaff($member)) {
                $clan_members .= " §f$member §r§f(§aCаptain§f ⩜)\n\n";
            } else {
                $clan_members .= " §f$member §r§f(§7Mеmber§f ⩗)\n\n";
            }
        }

        return $clan_members;
    }

    public static function getPublicClansList(): array {
        $list = [];
        foreach (Main::$clan_list as $clan) {
            if ($clan["is_public"]) array_push($list, $clan["name"]);
        }
        return $list;
    }

    public static function getTempClanDescription(string $clan_name): string {
        return Main::$temp_clans[$clan_name]["desc"];
    }

    public static function getClanDescription(string $clan_name): string {
        return Main::$clan_list[$clan_name]["desc"];
    }

    public static function clanAlreadyExists(string $clan_name): bool {
        return is_file("plugin_data/ClansInstitution/clans/" . strtolower($clan_name) . ".json"); // Si el archivo no existe, esto devolverá false
    }
}
