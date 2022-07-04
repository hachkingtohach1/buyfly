<?php

declare(strict_types=1); 

namespace FlyShop\Ki\task;
use pocketmine\player\Player;
use pocketmine\scheduler\Task;
use FlyShop\Ki\BuyFly;

class FlyTimeTask extends Task{
  
  private BuyFly $plugin;
  
 public function __construct(BuyFly $plugin){
    	$this->plugin = $plugin;
    }

    public function getOwningPlugin() : BuyFly {
    	return $this->plugin;
    }
    public function onRun() : void {
    	if(count($this->getOwningPlugin()->times->getAll()) >= 1){
    		foreach($this->getOwningPlugin()->times->getAll() as $p => $times){
                if($times !== "Forever"){
                    if($times == 0){
    			     	$this->getOwningPlugin()->times->remove($p);
    			     	if($this->getOwningPlugin()->getServer()->getPlayerByPrefix($p) instanceof Player){
    			     		$this->getOwningPlugin()->getServer()->getPlayerByPrefix($p)->sendMessage("§l§c•§e Fly của bạn đã hết giờ , Hãy Mua Thêm Bằng Cách Sử Dụng Lệnh§b /buyfly");
                             $this->PurePerms()->getUserDataMgr()->unsetPermission($player, "fly.cmd");
    			     	}
                    }
                }
                date_default_timezone_set('Asia/Ho_Chi_Minh');
                if(date("H:i:s") == "01:00:00"){
                    if($times !== "Forever"){
                        $this->getOwningPlugin()->times->set($p, $times - 1);
                        if($this->getOwningPlugin()->getServer()->getPlayerByPrefix($p) instanceof Player){
                            $this->getOwningPlugin()->getServer()->getPlayerByPrefix($p)->sendMessage("§l§c•§e FLY Của Bạn Còn§a $times Giờ §c•");
                        }
                    }
                }
    		}
            $this->getOwningPlugin()->times->save();
    	}
    }
}


