<?php 

namespace ClansInstitution\Events;

use ClansInstitution\Main;
use ClansInstitution\Utils\SearchUtils;
use pocketmine\event\entity\EntityDamageByEntityEvent;
use pocketmine\event\Listener;
use pocketmine\event\player\PlayerJoinEvent;
use pocketmine\player\Player;
use ServerProtect\Main as ServerProtect;


class EventListener implements Listener {

    public Main $main;

    public function __construct(Main $main) {
        $this->main = $main;
    }


    // considera mover esta funcion a AsyncTask luego
    
    public function onJoin(PlayerJoinEvent $event){
        
        $nick = strtolower($event->getPlayer()->getName());
        $path = "plugin_data/ClansInstitution/players/";
     
        if(!file_exists($path.$nick."json")) copy($path."template.json", $path."$nick.json");
    }


    /**
     * @priority LOWEST
     */

    public function onDamage(EntityDamageByEntityEvent $event):void{
        
        $damager = $event->getDamager();
        $victim = $event->getEntity();

        if($damager instanceof Player && $victim instanceof Player){

            $damager_nick = $damager->getName();
            $victim_nick = $victim->getName();

            if(SearchUtils::IsAssociatedToClan($damager_nick) && SearchUtils::IsAssociatedToClan($victim_nick)){
                if(SearchUtils::getClanName($damager_nick) === SearchUtils::getClanName($victim_nick)){

                    $world = $damager->getWorld()->getFolderName();

                    if($world === ServerProtect::PVP || $world === ServerProtect::RTP){
                        $event->cancel();
                        $damager->sendTip("⩣ §cYou cannot hаrm your clan §emembers §f");
                    }
                }
            }
        }

    }
}
