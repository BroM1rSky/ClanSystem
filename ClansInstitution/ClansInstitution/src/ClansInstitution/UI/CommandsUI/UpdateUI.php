<?php 

namespace ClansInstitution\UI\CommandsUI;

use ClansInstitution\Main;
use pocketmine\player\Player;
use ClansInstitution\Utils\GetForm;
use ClansInstitution\Utils\StoreUtils;
use ClansInstitution\Utils\SearchUtils;
use ClansInstitution\UI\Infrastructure\SetClan;
use ClansInstitution\Utils\Utils;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

class UpdateUI {

    public static function onManageNameUpdate(Player $player, mixed $data): void {
        $nick = $player->getName();

        if ($data != null && SearchUtils::isClanOwner($nick)) {
            $clan_name = $data[1]; // El nombre del clan introducido por el jugador

            if (SearchUtils::ClanAlreadyExists($clan_name)) {
                self::onSendNameUpdateForm($player, SetClan::CLAN_ALREADY_EXISTS);
                return;
            } elseif (strlen($clan_name) <= SetClan::NAME_MAX_LENGTH && strlen($clan_name) >= SetClan::NAME_MIN_LENGTH) {
                StoreUtils::updateClanName(SearchUtils::getClanName($nick), $clan_name);
                self::OnSendNameSuccessForm($player, $clan_name);
                Utils::sendClanMessage("§7(§f⨱§7)§f  §e" . $player->getName() . " §fhаs changed thе clan name to §a" . $clan_name . " §f⩇", $clan_name);            } else {
                self::onSendNameUpdateForm($player, SetClan::NAME_EXCEPTION);
                // Si el nombre no es válido, se enviará este formulario nuevamente hasta que el nombre sea válido o el jugador deje el formulario en NULL
            }
        }
    }

    public static function onManageDescUpdate(Player $player, mixed $data): void {
        if ($data === null) return;

        $nick = $player->getName();
        $clan_desc = $data[1]; // La descripción introducida por el jugador

        if (strlen($clan_desc) <= SetClan::DESCRIPTION_MAX_LENGTH && strlen($clan_desc) >= SetClan::DESCRIPTION_MIN_LENGTH) {
            if ($data != null && SearchUtils::isClanOwner($nick)) {
                StoreUtils::updateClanDescription(SearchUtils::getClanName($nick), $clan_desc);
                self::OnSendNameSuccessForm($player, SearchUtils::getClanName($nick));
                Utils::sendClanMessage("§7(§f⨱§7)§f  §e" . $player->getName() . " §fhаs changed the clan description §f⩇", SearchUtils::getClanName($nick));            }
        } else {
            self::OnSendDescriptionUpdateForm($player, SearchUtils::getClanName($nick), SetClan::DESCRIPTION_EXCEPTION);
            // Si la descripción no es válida, se enviará este formulario nuevamente hasta que sea válida o el jugador deje el formulario en NULL
        }
    }

    public static function OnSendFailForm(Player $player, string $error) {
        $form = GetForm::getFailForm();
        $form->setTitle("§f⩕ §l§aClаn Notifications §r§f⩕");
        $form->addLabel($error);
        $player->sendForm($form);
    }

    public static function OnSendNameSuccessForm(Player $player, string $clan_name) {
        $form = GetForm::getSuccessForm();
        $form->setTitle("§f⩰ §l§aUpdatе Clan §r§f⩰");
        $form->addLabel("\n       ⨺ §aClаn updated successfully! §f⨺");
        $form->addLabel("\n §f⨶ §9Namе: §e$clan_name\n\n §f⩆ §eDescription§f: " . SearchUtils::getClanDescription($clan_name));

        $player->sendForm($form);
        $pos = $player->getPosition();
        $player->getNetworkSession()->sendDataPacket(PlaySoundPacket::create("random.levelup", $pos->getX(), $pos->getY(), $pos->getZ(), 1, 1));
    }

    public static function OnSendDescriptionUpdateForm(Player $player, string $clan_name, string $error = "") {
        $form = GetForm::getUpdateDescriptionForm($clan_name);
    
        $form->setTitle("§f⩰ §l§aUpdatе Description §r§f⩰");
        $form->addLabel("\n            §f⩇ §aEntеr a new description §f⩇\n\n");
        $form->addInput("§fEnter the descriptiоn ⩆", "Type here!");
    
        $error === "" ? $form->addLabel("\n\n") : $form->addLabel("$error\n");
    
        $player->sendForm($form);
    }
    

    public static function onSendNameUpdateForm(Player $player, string $error = ""): void {
        $form = GetForm::getUpdateNameForm();

        $form->setTitle("§f⩰ §l§aUpdatе Name §r§f⩰");
        $form->addLabel("\n         §f⩇ §aChoоse a name for your clan §f⩇");
        $form->addInput("\n §fEnter the namе ⩇", "Type here!");
        

        $error === "" ? $form->addLabel("\n\n") : $form->addLabel("$error\n");

        $player->sendForm($form);
    }
}
