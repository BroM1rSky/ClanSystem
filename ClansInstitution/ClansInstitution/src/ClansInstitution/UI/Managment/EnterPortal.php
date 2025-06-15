<?php 

namespace ClansInstitution\UI\Managment;

use ClansInstitution\Main;
use ClansInstitution\UI\CommandsUI\ClanListUI;
use ClansInstitution\UI\CommandsUI\InvitationUI;
use ClansInstitution\UI\Infrastructure\SetClan;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;
use ClansInstitution\Utils\GetForm;
use ClansInstitution\Utils\SearchUtils;

class EnterPortal {

    public static function onSendInitForm(Player $player){
        
        $form = GetForm::getInitForm();
        
        $form->setTitle("§f⩰ §l§aCreаte Clan §r§f⩰");
        $form->setContent("\n               §fWelcоme §e".$player->getName()."\n \n");
        $form->addButton("§f⨻ §eCreate Clаn §r§f⨻");
        $form->addButton("§f⩆ §eInvitatiоns §f⩆ ");
        $form->addButton("§f⩉ §eManage Clаn §r§f⩉");
        $form->addButton(" §eView Clаns §r");
        

        $player->sendForm($form);
    }
    
    
    public static function OnManageInitForm(Player $player, mixed $data):void{

        if($data !== null){
            switch ($data) {
                case 0: SetClan::OnSendNameForm($player); break;
                case 1: InvitationUI::OnSendInvivationsForm($player); break;
                case 2: SearchUtils::IsAssociatedToClan($player->getName()) ? ManageClan::getSpecificForm($player) : InvitationUI::OnSendFailForm($player,InvitationUI::DONT_HAVE_INVITATIONS); break;
                case 3: ClanListUI::onSendList($player); break;
            }
        }
    }

}
