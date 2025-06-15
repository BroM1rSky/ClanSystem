<?php 

namespace ClansInstitution\UI\CommandsUI;

use ClansInstitution\Main;
use pocketmine\player\Player;
use ClansInstitution\Utils\GetForm;
use ClansInstitution\Utils\SearchUtils;

class ViewMembersUI {

    public static function OnSendMemberForm(Player $player, string $clan_name){
        $members_list = SearchUtils::getClanMembers($clan_name);
        $form = GetForm::getMemberListForm();
        
        $form->setTitle("§f⨔ §aClan Mеmbers §9$clan_name §f⨔");
        $form->setContent("\n$members_list\n\n");
        
        $player->sendForm($form);
    }


}

 
