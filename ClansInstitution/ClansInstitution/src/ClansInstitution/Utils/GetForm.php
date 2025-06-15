<?php 

namespace ClansInstitution\Utils;

use ClansInstitution\Commands\DemolishClan;
use ClansInstitution\Main;
use ClansInstitution\UI\CommandsUI\ClanListUI;
use ClansInstitution\UI\CommandsUI\InvitationUI;
use ClansInstitution\UI\CommandsUI\KickUI;
use ClansInstitution\UI\CommandsUI\StuffUI;
use ClansInstitution\UI\CommandsUI\TransferOwnership;
use ClansInstitution\UI\CommandsUI\UpdateUI;
use ClansInstitution\UI\Infrastructure\SetClan;
use ClansInstitution\UI\Managment\EnterPortal;
use ClansInstitution\UI\Managment\ManageClan;
use jojoe77777\FormAPI\CustomForm;
use jojoe77777\FormAPI\SimpleForm;
use pocketmine\player\Player;

class GetForm {

    public static function getInitForm(){
        $form = new SimpleForm(function (Player $player, $data) {
            EnterPortal::OnManageInitForm($player,$data);
        }); return $form;
    }

    public static function getNameForm(){
        $form = new CustomForm(function (Player $player, $data) {
            SetClan::OnManageNameForm($player,$data);
        }); return $form;
    }

    public static function getUpdateNameForm(){
        $form = new CustomForm(function (Player $player, $data) {
            UpdateUI::onManageNameUpdate($player,$data);
        }); return $form;
    }

    public static function getDescriptionForm(string $clan_name){
        $form = new CustomForm(function (Player $player, $data)  use($clan_name) {
            SetClan::OnManageDescriptionForm($player,$data,$clan_name);
        }); return $form;
    }

    public static function getUpdateDescriptionForm(string $clan_name){
        $form = new CustomForm(function (Player $player, $data)  use($clan_name) {
            UpdateUI::onManageDescUpdate($player,$data,$clan_name);
        }); return $form;
    }

    public static function getPrivacyForm(string $clan_name){
        $form = new CustomForm(function (Player $player, $data)  use($clan_name) {
            SetClan::OnManagePrivacyForm($player,$data,$clan_name);
        }); return $form;
    }

    public static function getSuccessForm(){
        $form = new CustomForm(function (Player $player, $data){
            SetClan::OnManageSuccessForm($player);
        }); return $form;
    }


    public static function getMemberForm(){
        $form = new SimpleForm(function (Player $player, $data) {
            ManageClan::OnManageMemberForm($player,$data);
        }); return $form;
    }

    public static function getOwnerForm(){
        $form = new SimpleForm(function (Player $player, $data) {
            ManageClan::OnManageOwnerForm($player,$data);
        }); 
        return $form;
    }

    public static function getStaffForm(){
        $form = new SimpleForm(
            function (Player $player, $data) {
                ManageClan::OnManageStaffForm($player,$data);
            }
        );
        return $form;
    }

    public static function getMemberListForm(){
        $form = new SimpleForm(function (Player $player, $data) {
        }); return $form;
    }


    public static function getPlayerListForm(array $players_list){
        $form = new CustomForm(function (Player $player, $data) use($players_list) {
            InvitationUI::onManageInviteSelection($player,$data,$players_list);
        }); return $form;
    }

    public static function getMembersListForm(array $players_list, string $clan_name){
        $form = new CustomForm(function (Player $player, $data) use($players_list, $clan_name) {
            KickUI::onManageKickForm($player,$data,$players_list, $clan_name);
        }); return $form;
    }

    public static function getKickStuffMembersForm(array $players_list, string $clan_name){
        $form = new CustomForm(function (Player $player, $data) use($players_list, $clan_name) {
            StuffUI::onManageKickStuffForm($player,$data,$players_list, $clan_name);
        }); return $form;
    }

    public static function getGrantStuffMembersForm(array $players_list, string $clan_name){
        $form = new CustomForm(function (Player $player, $data) use($players_list, $clan_name) {
            StuffUI::onManageGrantStuffForm($player,$data,$players_list, $clan_name);
        }); return $form;
    }

    public static function getTransferOwnershipForm(array $players_list, string $clan_name){
        $form = new CustomForm(function (Player $player, $data) use($players_list, $clan_name) {
            TransferOwnership::onManageTransferOwnership($player,$data,$players_list, $clan_name);
        }); return $form;
    }


    public static function getInvitationListForm(){
        $form = new CustomForm(function (Player $player, $data) {
            InvitationUI::OnManageInvitationsListForm($player,$data);
        }); return $form;
    }

  
    public static function getFailForm(){
        $form = new CustomForm(function (Player $player, $data) {});
        return $form;
    }

    public static function getConfirmationForm(){
        $form = new SimpleForm(function (Player $player, $data) {
            DemolishClan::onCommit($player,$data);
        }); return $form;
    }

    public static function getClanListForm(array $clan_list){
        $form = new SimpleForm(function (Player $player, $data) use($clan_list) {
            ClanListUI::onManageClanSelection($player,$data,$clan_list);
        }); return $form;
    }

    public static function getClanInfoChoiseForm(string $clan_name){
        $form = new SimpleForm(function (Player $player, $data) use($clan_name) {
            ClanListUI::onManageClanDataForm($player,$data,$clan_name);
        }); return $form;
    }
    
    public static function getClanStatsForm(){
        $form = new SimpleForm(function (Player $player, $data){
            // nada aqui weee quiero dormiiiir
        }); return $form;
    }

}