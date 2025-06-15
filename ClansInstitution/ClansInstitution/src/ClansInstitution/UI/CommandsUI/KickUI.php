<?php 

namespace ClansInstitution\UI\CommandsUI;

use ClansInstitution\Main;
use pocketmine\player\Player;
use ClansInstitution\Utils\GetForm;
use ClansInstitution\Utils\InvitationUtils;
use ClansInstitution\Utils\SearchUtils;
use ClansInstitution\Utils\StoreUtils;
use ClansInstitution\Utils\Utils;

class KickUI {

    public static function onManageKickForm(Player $player, mixed $data, array $members_list, string $clan_name): void {
        if ($data != null) {
            $invitation = $data[1]; // La elección del jugador entre los miembros
            $target_name = $members_list[$invitation];
            
            if (SearchUtils::isClanOwner($target_name) && $player->getName() === $target_name) {
                Main::getInstance()->getServer()->dispatchCommand($player, "clan leave");
                return;
            } elseif (SearchUtils::isClanOwner($target_name) && $player->getName() !== $target_name) {
                self::OnSendFailForm($player, "\n\n§f⩕ §cYou cannot expel the clan leаder (Are u ok bro?) §f⩕\n\n\n");
                return;
            }

            Utils::sendClanMessage("§7(§f⨱§7)§f  §e".$player->getName()." §fexpellеd §a$target_name §ffrom the clan §f⩗", $clan_name);            
            Utils::dropMember($target_name);

            if (SearchUtils::isClanStaff($target_name)) {
                Utils::dropStaffMember($target_name);
            }
            
            $clan_data = SearchUtils::getClanData($clan_name);
            
            StoreUtils::OnStoreJSON($clan_data, $clan_name); // Aplica cambios en el JSON
            Utils::dropAssociatedClan($target_name);

            self::OnSendSuccessForm($player, $target_name, $clan_name);
        }
    }

    public static function OnSendFailForm(Player $player, string $error) {
        $form = GetForm::getFailForm();
        $form->setTitle("§f⩕ §l§aClаn Notifications §r§f⩕");
        $form->addLabel($error);
        $player->sendForm($form);
    }

    public static function OnSendSuccessForm(Player $player, string $target_name, string $clan_name) {
        $form = GetForm::getSuccessForm();
        $form->setTitle("§f⩌ §l§cKick Playеr §r§f⩌");
        $form->addLabel("\n    §f⩕ §aYоu have kicked §e$target_name §afrom your clan §d$clan_name §f⩕\n\n\n");
            
        $player->sendForm($form);        
    }

    public static function onSendKickForm(Player $player) {
        $nick = $player->getName();

        if (!SearchUtils::IsAssociatedToClan($player->getName())) {
            return;
        }

        if (SearchUtils::isClanOwner($nick) || SearchUtils::isClanStaff($nick)) {
            $members_list = SearchUtils::getClanMembersArray(SearchUtils::getClanName($player->getName()));
            $form = GetForm::getMembersListForm($members_list, SearchUtils::getClanName($player->getName()));
            $form->setTitle("§f⩌ §l§cKick Playеr §r§f⩌");
            $form->addLabel("\n         §f⩕ §eWhо do you want to kick? §f⩕\n\n\n");
            $form->addDropdown(" §fSelect a playеr §f⨽", $members_list);            
            $form->addLabel("\n\n");
            $player->sendForm($form);
        } else {
            $error = "\n\n§f⩕ §cYou don't have permissiоn to kick players §f⩕\n\n\n";
            self::OnSendFailForm($player, $error);
        }
    }
}
