<?php 

namespace ClansInstitution\UI\CommandsUI;

use ClansInstitution\Main;
use pocketmine\player\Player;
use ClansInstitution\Utils\Utils;
use ClansInstitution\Utils\GetForm;
use ClansInstitution\Utils\StuffUtils;
use ClansInstitution\Utils\SearchUtils;
use ClansInstitution\Commands\ViewMembers;

class TransferOwnership {

    public static function onManageTransferOwnership(Player $player, mixed $data, array $members_list, string $clan_name): void {
        if ($data != null) {
            $invitation = $data[1]; // La elección del jugador entre los miembros
            $target_name = $members_list[$invitation];
            
            if (!SearchUtils::isClanOwner($target_name)) {
                StuffUtils::onTransferOwnership($target_name, $clan_name);
                self::OnSendSuccessGrantForm($player, $target_name, $clan_name);
            } else {
                self::OnSendFailForm($player, "\n\n    §f This member is alreаdy the owner of the clan §f\n\n");
            }                       
        }                    
    }

    public static function OnSendFailForm(Player $player, string $error) {
        $form = GetForm::getFailForm();
        $form->setTitle("§f⩕ §l§aClаn Notifications §r§f⩕");
        $form->addLabel($error);
        $player->sendForm($form);
    }

    public static function OnSendSuccessGrantForm(Player $player, string $target_name, string $clan_name) {
        $form = GetForm::getSuccessForm();
        $form->setTitle("§f §l§aTransfеr Clan Ownership §r§f");
        $form->addLabel("\n\n  §f §aYоu have granted the ownership to §d$target_name §f\n\n\n");        
        $player->sendForm($form);
        Utils::sendClanMessage("⨀ §e". $player->getName() . " §fhas transferred the rights of §9owner §fof the clan tо §a$target_name §f", $clan_name);
        
    }

    public static function onSendTransferOwnershipForm(Player $player): void {
        $nick = $player->getName();

        if (!SearchUtils::IsAssociatedToClan($nick)) {
            self::OnSendFailForm($player, ViewMembers::CLAN_NOT_FOUND);
            return;
        }

        if (!SearchUtils::isClanOwner($nick)) {
            self::OnSendFailForm($player, "\n\n§f⩕ §cYou are nоt the owner of the clan §f⩕\n\n\n");
            return;
        }

        $members_list = SearchUtils::getClanMembersArray(SearchUtils::getClanName($player->getName()));
        $form = GetForm::getTransferOwnershipForm($members_list, SearchUtils::getClanName($player->getName()));
        $form->setTitle("§f §l§aTransfer Clаn Ownership §r§f");
        $form->addLabel("\n§f⩆ §eWho do you wаnt to transfer the §aownership§e? §f⩆\n\n\n");
        $form->addDropdown(" §fSelect a clan membеr §f⨽", $members_list);
        
        $form->addLabel("\n\n");
        $player->sendForm($form);
    }
}
