<?php 

namespace ClansInstitution\Utils;

use ClansInstitution\Main;
use ClansInstitution\UI\CommandsUI\InvitationUI;
use ClansInstitution\UI\Infrastructure\SetClan;
use pocketmine\player\Player;
use pocketmine\utils\Config;

class InvitationUtils {

    public static function hasDedicatedInvitations(string $nick): bool {
        return isset(Main::$invitations_list[strtolower($nick)]);
    }

    /**
     * Esta función solo debe llamarse después de usar
     * la función hasDedicatedInvitations que ha devuelto true
     *
     * @param string $nick
     * @return array
     */
    public static function getDedicatedInvitations(string $nick): array {
        return Main::$invitations_list[strtolower($nick)];
    }

    public static function getInvitationsList(string $nick): array {
        $invitations_list = self::hasDedicatedInvitations($nick) 
            ? array_merge(self::getDedicatedInvitations($nick), SearchUtils::getPublicClansList()) 
            : SearchUtils::getPublicClansList();
        return array_values(array_unique($invitations_list));
    }

    public static function isValidToInvite(Player $sender, mixed $target): bool {

        if (!$target instanceof Player) {
            $sender->sendMessage("§f This plаyer is already §coffline ");
            return false;
        }

        if (!SearchUtils::IsAssociatedToClan($target->getName())) {
            return true;
        }

        if (SearchUtils::getClanName($sender->getName()) === SearchUtils::getClanName($target->getName())) {
            InvitationUI::OnSendFailForm($sender, "\n\n§f⩕ §e" . $target->getName() . "§c is аlready in your clan §f⩕");            return false;
        }

        if (SearchUtils::IsAssociatedToClan($target->getName())) {
            $error = "\n\n§f⩕ §e" . $target->getName() . "§c is аssociated with a clan §d" . Main::$associated_clan[$target->getName()] . ".\n\n§f⩕ §cThey must leave the clan to receive new invitations\n\n\n";
            InvitationUI::OnSendFailForm($sender, $error);
            return false;
        }

        return true;
    }

    public static function hasInvitations(Main $main, Player $player): bool {
        return count(self::getInvitationsList($player->getName())) == 0;
    }

    /**
     * Esta función solo debe usarse si se sabe al 100% que el jugador tiene alguna 
     * invitación dedicada 
     *
     * @param Player $target
     * @param string $clan_name
     * @return boolean
     */
    public static function wasInvited(string $nick, string $clan_name): mixed {
        return array_search($clan_name, Main::$invitations_list[strtolower($nick)]);
    }

    public static function onSendInvitation(Player $sender, Player $target, string $clan_name): void {

        $nick = strtolower($target->getName());

        if (!self::hasDedicatedInvitations($nick) || self::wasInvited($nick, $clan_name) === false) {
            Main::$invitations_list[$nick][] = $clan_name;
            $target->sendMessage(" §e" . $sender->getName() . "§a hаs invited you to the clan §b$clan_name");
            InvitationUI::OnSendFailForm($sender, "\n\n §fPlayеr §e" . $target->getName() . "\n\n §f⨺ §aSuccessful invitation to your clan §f⨺ \n\n");
            Utils::sendClanMessage("§7(§f⨱§7)§f  §e" . $sender->getName() . " §ahаs invited §d" . $target->getName() . " §ato your clan §f⩅", $clan_name);

        } elseif (self::wasInvited($nick, $clan_name) !== false) {
            // Haremos lo mismo que arriba, pero no incrementaremos las invitaciones
            $target->sendMessage(" §e" . $sender->getName() . "§a has invitеd you to the clan §b$clan_name");
            InvitationUI::OnSendFailForm($sender, "\n\n §fPlayеr §e" . $target->getName() . "\n\n §f⨺ §aSuccessful invitation to your clan §f⨺ \n\n");
            Utils::sendClanMessage("§7(§f⨱§7)§f  §e" . $sender->getName() . " §ahаs invited §d" . $target->getName() . " §ato your clan §f⩅", $clan_name);
            
        }
    }

    public static function dropDedication(string $nick, string $clan_name): void {

        $nick = strtolower($nick);

        if (self::hasDedicatedInvitations($nick) && isset(Main::$invitations_list[$nick])) {
            $index = array_search($clan_name, Main::$invitations_list[$nick]);
            if ($index !== false) {
                unset(Main::$invitations_list[$nick][$index]);
                sort(Main::$invitations_list[$nick]);
            }
        }
    }
}
