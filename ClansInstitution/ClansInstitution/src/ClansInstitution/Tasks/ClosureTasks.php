<?php 

namespace ClansInstitution\Tasks;

use ClansInstitution\Main;
use pocketmine\scheduler\ClosureTask;

class ClosureTasks {

    public static function onExample():ClosureTask{
        
        return new  ClosureTask(function() :void { 
            //code here
        });
    }


}