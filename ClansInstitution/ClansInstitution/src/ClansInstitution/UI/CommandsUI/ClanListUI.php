<?php 

namespace ClansInstitution\UI\CommandsUI;

use ClansInstitution\Main;
use pocketmine\player\Player;
use ClansInstitution\Utils\GetForm;
use ClansInstitution\Utils\SearchUtils;
use pocketmine\utils\Config;

class ClanListUI {

    public static function getClanNamesList():array{
        return array_keys(Main::$clan_list);
    }

    public static function onSendList(Player $player):void{
        
        $clan_list = self::getClanNamesList();
        $form = GetForm::getClanListForm($clan_list);
        
        $form->setTitle("  §l§dClаn List §r§f");
        
        foreach($clan_list as $clan_name){
            $form->addButton("§f⨶§r $clan_name §f⨷");
        }   
        $player->sendForm($form);
    }

    public static function onManageClanSelection(Player $player, mixed $data, array $clan_list):void{
        
        if($data !== NULL) {
            $selected_clan = $clan_list[$data];
            self::onSendChooseClanInfoForm($player,$selected_clan);
        }
    }

    public static function onSendChooseClanInfoForm(Player $player, string $clan_name):void{
        $form = GetForm::getClanInfoChoiseForm($clan_name);
        $form->setTitle(" §f⨶§r $clan_name §r§f⨶");
        $form->addButton("§f⩆ §eView Stаts §f⩆");
        $form->addButton("§f⩉ §eMеmber List §f⩉");
        $form->addButton("§f⩗ §fDеclare §9War §4Clan §f⩜");
        $player->sendForm($form);
    }

    public static function onManageClanDataForm(Player $player, mixed $data, string $clan_name):void{
        
        if($data !== NULL){
            switch ((int) $data) {
                case 0: self::onViewClanStats($player,$data,$clan_name); break;
                case 1: ViewMembersUI::OnSendMemberForm($player,$clan_name); break;
                case 2: InvitationUI::OnSendFailForm($player, "\n\n    ⩖ §l§9Clan §4Wars§b are in development §r§f⩔\n\n    Wе aim to finish the CW development ⩋\n    by the end of summer §f⨿§f.\n    If you'd like to help ⩈,\n    support us in our §bVK§f group\n\n    §l§bvk.com§f/§5seriksworld");
            }
        }
    }

    public static function onViewClanStats(Player $player, mixed $data, string $clan_name):void{

        if($data === NULL) return;
        
        $kills = 0; $deaths = 0; $money = 0; $set_home = "?"; $wins = "?"; $loses = "?";

        $members = SearchUtils::getClanMembersArray($clan_name);

        foreach ($members as $member) {
            $member = strtolower($member); 
            $conf = new Config("plugin_data/PlayerInfoRequest/$member/$member.json", Config::JSON);
            $kills += $conf->get("kills"); 
            $deaths += $conf->get("death");
        }

        foreach ($members as $member) {
            $member = strtolower($member); 
            $conf = new Config("plugin_data/economition/$member/$member.json", Config::JSON);
            $money += $conf->get("qtt_paper");
        }

        self::onSendViewStatsForm($player, $clan_name, $kills, $deaths, $money, $set_home, $wins, $loses);
    }

    public static function onSendViewStatsForm(Player $player, string $clan_name, int $kills, int $deaths, int $money, mixed $set_home, mixed $wins, mixed $loses):void{
        $form = GetForm::getClanStatsForm();
        $form->setTitle(" §f⨶§r $clan_name §r§f⨶");
        $form->setContent("\n                    Stаtistics:\n\n  §fEnemies Killed: §e$kills §f⩥\n\n  §fDeaths: §e$deaths §f⨴\n\n  §fMoney: §e$money §f⩐\n\n\n  §fClan Home: §e$set_home §f⨱\n\n  §fWins in §9C§4W§f: §e$wins §f\n\n  §fLosses in §9C§4W§f: §e$loses §f⨇");
        $player->sendForm($form);
    }
}
