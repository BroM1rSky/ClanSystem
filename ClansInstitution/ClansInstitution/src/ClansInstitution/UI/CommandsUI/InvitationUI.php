<?php 

namespace ClansInstitution\UI\CommandsUI;

use ClansInstitution\Commands\InviteClan;
use ClansInstitution\Main;
use pocketmine\player\Player;
use ClansInstitution\Utils\GetForm;
use ClansInstitution\Utils\InvitationUtils;
use ClansInstitution\Utils\SearchUtils;
use ClansInstitution\Utils\Utils;

class InvitationUI {

    public const DONT_HAVE_INVITATIONS = "\n\n      §f⩕ §cYоu have no pending invitations! §f⩕\n\n\n";

    public static function OnManageInvitationsListForm(Player $player, mixed $data):void{
        if($data != null){
            $invitation = $data[1]; // La elección del jugador entre todas las invitaciones
            $clans_names_list = [];

            foreach (InvitationUtils::getInvitationsList($player->getName()) as $clan) {
                array_push($clans_names_list, $clan);
            }

            $choosed_clan_name = $clans_names_list[$invitation];
            
            Utils::OnAssociateToClan($player->getName(), $choosed_clan_name);
            self::OnSendSuccessForm($player, $choosed_clan_name);
        }
    }

    public static function onManageInviteSelection(Player $player, mixed $data, array $players_list):void{
        if($data !== null){
            $choosed_player = $players_list[$data[1]];
            $target = Main::getInstance()->getServer()->getPlayerExact($choosed_player);
            if(InvitationUtils::isValidToInvite($player, $target)) {
                InvitationUtils::onSendInvitation($player, $target, SearchUtils::getClanName($player->getName()));
            }
        }
    }

    public static function OnSendInvivationsForm(Player $player){
        $main = Main::getInstance();

        if(InvitationUtils::hasInvitations($main, $player)){
            self::OnSendFailForm($player, self::DONT_HAVE_INVITATIONS);
        } elseif(SearchUtils::IsAssociatedToClan($player->getName())){
            $error = "\n\n§f⩕ §cYou are alrеady in the clan §d".Main::$associated_clan[$player->getName()].".\n\n§f⩕ §cUsе §e/clan leave §cto leave the clan \n\n\n";
            self::OnSendFailForm($player, $error);
        } else {
            $form = GetForm::getInvitationListForm($main);
            $form->setTitle("§f⩆ §l§aClan Invitаtions §r§f⩆");
            $form->addLabel("\n     §f⩅ §aWhich clаn do you want to join? §f⩅\n\n\n");
            $form->addDropdown(" §fSelect a clаn §f⨽", InvitationUtils::getInvitationsList($player->getName()));            
            $form->addLabel("\n\n");            
            $player->sendForm($form);
        }
    }

    public static function OnSendFailForm(Player $player, string $error){
        $form = GetForm::getFailForm();
        $form->setTitle("§f⩕ §l§aClаn Notifications §r§f⩕");
        $form->addLabel($error);
        $player->sendForm($form);
    }

    public static function onSendConfirmationForm(Player $player){
        $form = GetForm::getConfirmationForm();
        $form->setTitle("§f⩕ §l§aArе you sure? §r§f⩕");
        $form->setContent("\n\n      ⨱ §l§fDеlete the clan §b". SearchUtils::getClanName($player->getName()) ."§f? §r§f⨱\n\n     §l§fYоu won't be able to recover the clan §c§n\n \n");
        $form->addButton("§cDelеte§f clan §f⩌");
        $form->addButton("§aDоn't delete§f clan §f⨺");
        
        $player->sendForm($form);
    }

    public static function OnSendSuccessForm(Player $player, string $clan_name){
        $form = GetForm::getSuccessForm();
        $form->setTitle("§f⩆ §l§aClan Invitatiоns §r§f⩆");
        $form->addLabel("\n               §f⨧ §aCongratulatiоns! §f⨧\n\n    §f⩅ §aYоu hаve joined the clan §e$clan_name §f⩅\n\n\n");        
        $player->sendForm($form);
        InvitationUtils::dropDedication($player->getName(), $clan_name);
        Utils::sendClanMessage("§7(§f⨱§7)§f  §e".$player->getName()." §a has joined your clаn §f⩅", $clan_name);

    }

    public static function onSendSelectionPlayer(Player $player){
        $nick = $player->getName();

        if(SearchUtils::isClanOwner($nick) || SearchUtils::isClanStaff($nick)){
            $list = Utils::getOnlinePlayersList();
            $form = GetForm::getPlayerListForm($list);
            $form->setTitle("§f⩆ §l§aClаn Invitations §r§f⩆");
            $form->addLabel("\n       §f⩅ §aWho dо you want to invite? §f⩅\n\n\n");
            $form->addDropdown(" §fSеlect a player §f⨽", $list);
            
            $form->addLabel("\n\n");            
            $player->sendForm($form);
        } else {
            $error = "\n\n§f⩕ §cYоu do not have permission to invite players §f⩕\n\n\n";
            InvitationUI::OnSendFailForm($player, $error);
        }
    }
}
