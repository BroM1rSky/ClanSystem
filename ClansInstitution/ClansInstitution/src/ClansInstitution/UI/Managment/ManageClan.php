<?php 

namespace ClansInstitution\UI\Managment;

use ClansInstitution\Commands\DemolishClan;
use ClansInstitution\Main;
use ClansInstitution\UI\CommandsUI\InvitationUI;
use ClansInstitution\UI\CommandsUI\KickUI;
use ClansInstitution\UI\CommandsUI\StuffUI;
use ClansInstitution\UI\CommandsUI\TransferOwnership;
use ClansInstitution\UI\CommandsUI\UpdateUI;
use ClansInstitution\UI\Infrastructure\SetClan;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use ClansInstitution\Utils\GetForm;
use ClansInstitution\Utils\SearchUtils;

class ManageClan {

    public static function OnSendMemberForm(Player $player){
        
        $form = GetForm::getMemberForm();
        $form->setTitle("§f⩉ §l§aClan Managеment §r§f⩉");

        $form->addButton(" §eView Mеmbers ");
        $form->addButton("§f⩌ §eLeavе Clan §f⩌");
        
        
        $player->sendForm($form);
    }

    public static function OnSendStaffForm(Player $player){
        
        $form = GetForm::getStaffForm();
        $form->setTitle("§f⩉ §l§aClаn Management §r§f⩉");

        $form->addButton(" §eView Mеmbers ");
        $form->addButton("§f⩌ §eLeavе Clan §f⩌");
        $form->addButton("§f⩅ §eInvitе Player §f⩅");
        $form->addButton("§f⩕ §8Kiсk Player §f⩕");

        
        $player->sendForm($form);
    }

    public static function OnSendOwnerForm(Player $player){
        
        $form = GetForm::getOwnerForm();
        $form->setTitle("§f⩉ §l§aClаn Management §r§f⩉");

        $form->addButton(" §eViеw Members ");
        $form->addButton("§f⩅ §eInvitе Player §f⩅");
        $form->addButton("§f⩕ §eKiсk Player §f⩕");
        $form->addButton("§f⩗ §ePrоmote to Captain §f⩗");
        $form->addButton("§f⩕ §eRemоve Captain §f⩕");
        $form->addButton("§f⩇ §eChangе Clan Name §f⩇");
        $form->addButton("§f⩆ §eChangе Clan Description §f⩆");
        $form->addButton("§f §eTransfеr Clan Ownership §f");
        $form->addButton("§c⨴ §eDeletе Clan §c⨴");
        
        
        $player->sendForm($form);
    }

    public static function getSpecificForm(Player $player):void{

        $nick = $player->getName();
        $main = Main::getInstance();

        if(SearchUtils::isClanOwner($nick)){
            self::OnSendOwnerForm($player,$main); // Enviar formulario de propietario
        }
        elseif(SearchUtils::isClanStaff($nick)){
            self::OnSendStaffForm($player,$main); // Enviar formulario de personal
        }
        else{
            self::OnSendMemberForm($player,$main); // Enviar formulario de miembro
        }
    }

    public static function OnManageMemberForm(Player $player, mixed $data):void{
        if($data !== null){
            
            $main = Main::getInstance();

            if($data === 0){
                $main->getServer()->dispatchCommand($player,"clan members");
            }
            elseif($data === 1){
                $main->getServer()->dispatchCommand($player,"clan leave");
            }
        }
    }

    public static function OnManageStaffForm(Player $player, mixed $data):void{
        if($data !== null){
            
            $main = Main::getInstance();

            if($data === 0){
                $main->getServer()->dispatchCommand($player,"clan members");
            }
            elseif($data === 1){
                $main->getServer()->dispatchCommand($player,"clan leave");
            }
            elseif($data === 2){
                InvitationUI::onSendSelectionPlayer($player);            
            }
            elseif($data === 3){
                KickUI::onSendKickForm($player);
            }

        }
    }

    public static function OnManageOwnerForm(Player $player, mixed $data):void{

        if($data !== null){
            
            $main = Main::getInstance();

            if($data === 0){
                $main->getServer()->dispatchCommand($player,"clan members");
            }
            elseif($data === 1){
                InvitationUI::onSendSelectionPlayer($player);
            }
            elseif($data === 2){
                KickUI::onSendKickForm($player);
            }
            elseif($data === 3){
                StuffUI::onSendGrantStuffForm($player);
            }
            elseif($data === 4){
                StuffUI::onSendRefuseStuffForm($player);
            }
            elseif($data === 5){
                UpdateUI::onSendNameUpdateForm($player);
            }
            elseif($data === 6){
                UpdateUI::OnSendDescriptionUpdateForm($player,SearchUtils::getClanName($player->getName()));
            }
            elseif($data === 7){
                TransferOwnership::onSendTransferOwnershipForm($player);
            }
            elseif($data === 8){
                DemolishClan::onExecute($player);
            }

        }
        
    }

}
