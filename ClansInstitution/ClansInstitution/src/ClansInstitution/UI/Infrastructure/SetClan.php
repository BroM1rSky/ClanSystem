<?php 

namespace ClansInstitution\UI\Infrastructure;

use ClansInstitution\Main;
use pocketmine\player\Player;
use ClansInstitution\Utils\GetForm;
use ClansInstitution\Utils\SearchUtils;
use ClansInstitution\Utils\StoreUtils;
use ClansInstitution\Utils\Utils;
use pocketmine\network\mcpe\protocol\PlaySoundPacket;

class SetClan {

    public const NAME_MAX_LENGTH = 15;
    public const NAME_MIN_LENGTH = 2;
    public const NAME_EXCEPTION = "§f⩕ §cInvalid nаme! §f⩕";
    public const CLAN_ALREADY_EXISTS = "⩕ §cThis clаn already exists! §f⩕";    

    public const DESCRIPTION_MAX_LENGTH = 180;
    public const DESCRIPTION_MIN_LENGTH = 10;
    public const DESCRIPTION_EXCEPTION = "§f⩕ §cDescription tоo short §f⩕";

    public static function OnSendNameForm(Player $player, string $error = ""){
        
        $form = GetForm::getNameForm();
        
        $form->setTitle("§f⩰ §l§aCreate Clаn §r§f⩰");
        $form->addLabel("\n         §f⩇ §aEntеr thе name of the Clan §f⩇");
        $form->addInput("\n §fNamе ⩇", "Writе here");
        
        
        $error === "" ? $form->addLabel("\n\n") : $form->addLabel("$error\n");

        $player->sendForm($form);
    }

    public static function OnSendDescriptionForm(Player $player, string $clan_name, string $error = ""){
        
        $form = GetForm::getDescriptionForm($clan_name);
        
        $form->setTitle("§f⩰ §l§aCreаte Clan §r§f⩰");
        $form->addLabel("\n              §f⩇ §aClаn Description §f⩇\n\n");
        $form->addInput("§fDescriptiоn ⩆", "Writе here");
        
        
        $error === "" ? $form->addLabel("\n\n") : $form->addLabel("$error\n");

        $player->sendForm($form);
    }

    public static function OnSendPrivacyForm(Player $player, string $clan_name){
        
        $form = GetForm::getPrivacyForm($clan_name);
        
        $form->setTitle("§f⩰ §l§aCreatе Clan §r§f⩰");
        $form->addLabel("\n          §aYоur clan is almost ready! \n\n");
        $form->addDropdown(" §fHow can playеrs join the clan? §f⩅", ["Invitatiоn ⩆", "No invitatiоn ⩋"]);
        
        $form->addLabel("\n\n");

        $player->sendForm($form);
    }
    
    public static function OnSendSuccesForm(Player $player, string $clan_name){
        $form = GetForm::getSuccessForm();
        $form->setTitle("§f⩰ §l§aCreatе Clan §r§f⩰");
        $form->addLabel("\n          ⨺ §aClаn created successfully! §f⨺");
        $form->addLabel("\n §f⨶ §9Namе: §e$clan_name\n\n §f⩆ §eDescriptiоn§f: ".SearchUtils::getTempClanDescription($clan_name));
        
        $player->sendForm($form);
        $pos = $player->getPosition();
        $player->getNetworkSession()->sendDataPacket(PlaySoundPacket::create("random.levelup", $pos->getX(), $pos->getY(), $pos->getZ(), 1, 1));
        Main::getInstance()->getServer()->broadcastMessage("⨱ §a".$player->getName()." §fhas creatеd the clan §d$clan_name §f⨱");
    }
    
    public static function OnManageNameForm(Player $player, mixed $data):void{
        
        if($data === null ){
            return;
        }
            
        $clan_name = $data[1]; // Nombre del clan que el jugador introdujo en el primer formulario

        if(SearchUtils::ClanAlreadyExists($clan_name)) {
            self::OnSendNameForm($player,self::CLAN_ALREADY_EXISTS);
        }
        elseif(SearchUtils::IsAssociatedToClan($player->getName())){ // Si el jugador ya es miembro de algún clan
            $player->sendMessage(" §7You are already in the clаn §d".Main::$associated_clan[$player->getName()]."\n§7 Use §e/clаn leave§7 to leave the clan");
        }
        elseif(strlen($clan_name) <= self::NAME_MAX_LENGTH && strlen($clan_name) >= self::NAME_MIN_LENGTH){

            StoreUtils::storeTempClanName($clan_name); // Guardar el nombre del clan temporalmente
            self::OnSendDescriptionForm($player,$clan_name); // Enviar el siguiente formulario
        
        }else{
            self::OnSendNameForm($player,self::NAME_EXCEPTION);
            // Si el nombre no es válido, enviamos este formulario nuevamente hasta que el nombre sea válido o el jugador lo abandone
        }
        
    }

    public static function OnManageDescriptionForm(Player $player, mixed $data, string $clan_name):void{
        
        if($data != null){

            $clan_desc = $data[1]; // Descripción que el jugador introdujo en el segundo formulario

            if(strlen($clan_desc) <= self::DESCRIPTION_MAX_LENGTH && strlen($clan_desc) >= self::DESCRIPTION_MIN_LENGTH ){

                StoreUtils::storeTempDescription($clan_name, $clan_desc); // Guardar la descripción del clan temporalmente
                self::OnSendPrivacyForm($player,$clan_name); // Enviar el siguiente formulario

            }else{
                self::OnSendDescriptionForm($player,$clan_name,self::DESCRIPTION_EXCEPTION);
                // Si la descripción no es válida, enviamos este formulario nuevamente hasta que sea válida o el jugador lo abandone
            }   
        }
    }

    public static function OnManagePrivacyForm(Player $player, mixed $data, string $clan_name):void{
        
        if($data != null){

            $main = Main::getInstance();
            $is_public = $data[1]; // Elección del jugador, si el clan será público o no

            if($is_public === 0 || $is_public === 1 ){ # 0 = Necesita invitación (no es público) : # 1 = Sin invitación (público y se puede usar /clan join)
                
                StoreUtils::storeTempPublicDetails($clan_name,$is_public); // Configurar el clan como público o no
                StoreUtils::storeTempOwner($clan_name,$player->getName()); // Añadir propietario temporal
                self::OnSendSuccesForm($player,$clan_name); // Enviar el formulario final de éxito
                StoreUtils::onMergeTempData($clan_name,$main); // Eliminar datos temporales y guardar la info en el archivo JSON y en el array $clan_list

            }else{
                // Todo debería estar bien, y esta opción es muy poco común. Pero si ocurre, necesitamos saberlo.
               $player->sendMessage(" §cSоmething went wrong. Please contact the support and give them this [CLAN ManagePrivacy]");
            }
        }
    }

    public static function OnManageSuccessForm(Player $player):void{
        // código
    }
    
}
