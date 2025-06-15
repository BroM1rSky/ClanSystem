<?php 

namespace ClansInstitution\UI\CommandsUI;

use ClansInstitution\Commands\ViewMembers;
use ClansInstitution\Main;
use pocketmine\player\Player;
use ClansInstitution\Utils\GetForm;
use ClansInstitution\Utils\SearchUtils;
use ClansInstitution\Utils\StoreUtils;
use ClansInstitution\Utils\StuffUtils;
use ClansInstitution\Utils\Utils;

class StuffUI {

    public static function onManageKickStuffForm(Player $player, mixed $data, array $stuff_list, string $clan_name): void {
        if ($data != null) {
            $invitation = $data[1]; // La elección del jugador entre los miembros
            $target_name = $stuff_list[$invitation];
            
            if (SearchUtils::isClanStaff($target_name)) {
                StuffUtils::onRefuseStuff($target_name); 
                $clan_data = SearchUtils::getClanData($clan_name);            
                StoreUtils::OnStoreJSON($clan_data, $clan_name); // Aplica cambios en el JSON
                self::OnSendSuccessKickForm($player, $target_name, $clan_name);            
            }                       
        }
    }

    public static function onManageGrantStuffForm(Player $player, mixed $data, array $stuff_list, string $clan_name): void {
        if ($data != null) {
            $invitation = $data[1]; // La elección del jugador entre los miembros
            $target_name = $stuff_list[$invitation];
            
            if (!SearchUtils::isClanStaff($target_name)) {
                StuffUtils::onGrandStuff($target_name, $clan_name); 
                $clan_data = SearchUtils::getClanData($clan_name);            
                StoreUtils::OnStoreJSON($clan_data, $clan_name); // Aplica cambios en el JSON
                self::OnSendSuccessGrantForm($player, $target_name, $clan_name);            
            } else {
                self::OnSendFailForm($player, "\n\n      §f §cThis player is alrеady a §aCaptain §f⩗\n\n");            
            }                       
        }
    }

    public static function OnSendFailForm(Player $player, string $error) {
        $form = GetForm::getFailForm();
        $form->setTitle("§f⩕ §l§aClаn Notifications §r§f⩕");
        $form->addLabel($error);
        $player->sendForm($form);
    }

    public static function OnSendSuccessKickForm(Player $player, string $target_name, string $clan_name) {
        $form = GetForm::getSuccessForm();
        $form->setTitle("  §f⩌ §l§4Depromote Captain §r§f⩌");
        $form->addLabel("\n  §f⩕ §cYou hаve removed the §eCaptain §cpower from §d$target_name §f⩗\n\n\n");        
        $player->sendForm($form);
        Utils::sendClanMessage("§7(§f⨱§7)§f  §e".$player->getName()." §fremоved the Captain power from §a$target_name §f⩕", $clan_name);
    }

    public static function OnSendSuccessGrantForm(Player $player, string $target_name, string $clan_name) {
        $form = GetForm::getSuccessForm();
        $form->setTitle(" §f⩗ §l§aPromоte to Captain §r§f⩗");
        $form->addLabel("\n\n  §f⨺ §aYоu have promoted §d$target_name §f⨺\n\n\n");        
        $player->sendForm($form);
        Utils::sendClanMessage("§7(§f⨱§7)§f  §e".$player->getName()." §fprоmoted §a$target_name §fto the rank of §4Captain §f⩗", $clan_name);
        
    }

    public static function onSendRefuseStuffForm(Player $player): void {
        $nick = $player->getName();
        $stuff_list = SearchUtils::getClanStaffMembers(SearchUtils::getClanName($player->getName()));

        if (count($stuff_list) === 0) {
            self::OnSendFailForm($player, "\n\n      §f⩕ §cThere is no captains in your clan yеt §f⩗\n\n");
            return;
        }

        if (!SearchUtils::IsAssociatedToClan($player->getName())) {
            self::OnSendFailForm($player, ViewMembers::CLAN_NOT_FOUND);
            return;
        }

        if(SearchUtils::isClanOwner($nick)) {
            $form = GetForm::getKickStuffMembersForm($stuff_list, SearchUtils::getClanName($player->getName()));
            $form->setTitle("§f⩌ §l§cDepromote Captаin Powers §r§f⩌");
            $form->addLabel("\n         §f⩕ §eWho dо you want to depromote? §f⩕\n\n\n");
            $form->addDropdown(" §fSelect a playеr §f⨽", $stuff_list);
            $form->addLabel("\n\n");
            $player->sendForm($form);
        }else{
            $error = "\n\n§f⩕ §cYou do not have permission to depromоte captains §f⩕\n\n\n";
            self::OnSendFailForm($player, $error);
        }
    }

    public static function onSendGrantStuffForm(Player $player): void {
        $nick = $player->getName();

        if (!SearchUtils::IsAssociatedToClan($player->getName())) {
            self::OnSendFailForm($player, ViewMembers::CLAN_NOT_FOUND);
            return;
        }

        if (SearchUtils::isClanOwner($nick)) {
            $members_list = SearchUtils::getClanMembersArray(SearchUtils::getClanName($player->getName()));
            $form = GetForm::getGrantStuffMembersForm($members_list, SearchUtils::getClanName($player->getName()));
            $form->setTitle(" §f⩆ §l§aAdd Captаin §r§f⩗");
            $form->addLabel("\n         §f⩆ §eWhо do you want to §apromote§e? §f⩆\n\n\n");
            $form->addDropdown(" §fSelect a playеr §f⨽", $members_list);
            
            $form->addLabel("\n\n");
            $player->sendForm($form);
        } else {
            $error = "\n\n§f⩕ §cYou do not have permission tо add captains §f⩕\n\n\n";
            self::OnSendFailForm($player, $error);
        }
    }
}
