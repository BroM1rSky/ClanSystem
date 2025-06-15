<?php

namespace ClansInstitution;

use ClansInstitution\Commands\DemolishClan;
use ClansInstitution\Commands\InviteClan;
use ClansInstitution\Commands\LeaveClan;
use ClansInstitution\Commands\ViewMembers;
use ClansInstitution\Events\EventListener;
use ClansInstitution\UI\CommandsUI\InvitationUI;
use ClansInstitution\UI\CommandsUI\KickUI;
use ClansInstitution\UI\CommandsUI\StuffUI;
use ClansInstitution\UI\CommandsUI\TransferOwnership;
use ClansInstitution\UI\Managment\EnterPortal;
use ClansInstitution\Utils\MapUtils;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;
use pocketmine\event\Listener;
use pocketmine\player\Player;
use pocketmine\plugin\PluginBase;


class Main extends PluginBase implements Listener {

    public const RTP = "world2";
    public const SPAWN = "PurpleFantasy";
    public const PVP = "";
    public const CS_1 = "Dust2";
    public const CS_2 = "Wars";
    public const DUEL = "arena1";
    public const NETHER = "ugly_nether";


    public static $instance;

    public static array $clan_list = [];
    public static array $temp_clans = [];
    public static array $associated_clan = [];
    public static array $invitations_list = [];

    public function onEnable():void {

        MapUtils::fetchDataToArray($this);
        MapUtils::fetchAssociations($this);
        self::$instance = $this;

        $this->getLogger()->info("§aPlugin §4ClansInstitution §e---> §aLoaded Soo Successfully! §e>:D");
        $this->getServer()->getPluginManager()->registerEvents(new EventListener($this),$this);
    }
    
    public static function getInstance():self{
        return self::$instance;
    }

    public function onCommand(CommandSender $sender, Command $command, string $label, array $args): bool{
        if($sender instanceof Player){
            if($command->getName() === "clan"){
                self::OnManage($sender,$args);
            }
        }
        return true;
    }

    public static function OnManage(Player $player, array $args){

        if(count($args) === 0) {
            EnterPortal::onSendInitForm($player);
        }else{
            switch ($args[0]) {

                case "leave": LeaveClan::OnLeave($player,$args); break;
                case "members": ViewMembers::OnView($player,$args);break;
                case "invite": InviteClan::OnInvite($player,$args); break;
                case "kick": KickUI::onSendKickForm($player); break;
                case "join": InvitationUI::OnSendInvivationsForm($player); break;
                case "grant": StuffUI::onSendGrantStuffForm($player);break;
                case "refuse": StuffUI::onSendRefuseStuffForm($player);break;
                case "owner": TransferOwnership::onSendTransferOwnershipForm($player);break;
                case "remove": DemolishClan::onExecute($player);break;

                default: EnterPortal::onSendInitForm($player);break;
            }
        }


    }
}