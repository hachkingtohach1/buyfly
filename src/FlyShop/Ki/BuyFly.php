<?php
namespace FlyShop\Ki;
use pocketmine\Server;
use pocketmine\player\Player;
use pocketmine\event\Listener;
use pocketmine\plugin\PluginBase;
use pocketmine\utils\Config;
use FlyShop\Ki\task\FlyTimeTask;
use pocketmine\command\CommandSender;
use pocketmine\command\Command;

class BuyFly extends PluginBase implements Listener {

	public static $instance;

	public static function getInstance() : self {
		return self::$instance;
	}


	public function onLoad() : void {
		$this->times = new Config($this->getDataFolder() . "times.yml", Config::YAML);
		self::$instance = $this;
	}

	public function onEnable() : void {
		$this->getServer()->getPluginManager()->registerEvents($this, $this);
        $this->pp = $this->getServer()->getPluginManager()->getPlugin("PurePerms");
        $this->money = $this->getServer()->getPluginManager()->getPlugin("EconomyAPI");
        $this->coin = $this->getServer()->getPluginManager()->getPlugin("CoinAPI");
        $this->times = new Config($this->getDataFolder() . "times.yml", Config::YAML);
        $this->getScheduler()->scheduleRepeatingTask(new FlyTimeTask($this), 20);
	}

	public function getTime(){
		return $this->times;
	}

	public function PurePerms(){
		return $this->pp;
	}

	public function getTimeRank(Player $player){
		if($this->times->exists($player->getName())){
			if($this->times->get($player->getName()) == "Forever"){
				return "Vĩnh Viễn";
			}else{
            	return $this->times->get($player->getName());
            }
		}else{
			return 0;
		}
	}

	public function addTime(Player $player, float|int $time){
		if($this->times->exists($player->getName())){
			if($this->times->get($player->getName()) == "Forever") return;
			$this->times->set($player->getName(), $this->times->get($player->getName()) + $time);
			$this->times->save();
		}else{
			$this->times->set($player->getName(), $time);
			$this->times->save();
		}
	}

	public function reduceTime(Player $player, float|int $time){
		if($this->times->exists($player->getName())){
			if($this->times->get($player->getName()) == "Forever") return;
			$this->times->set($player->getName(), $this->times->get($player->getName()) - $time);
			$this->times->save();
		}
	}

	public function setForever(Player $player, bool $status = true){
		if($status == true){
			$this->times->set($player->getName(), "Forever");
			$this->times->save();
		}
	}
	public function onCommand(CommandSender $sender ,Command $cmd ,string $label ,array $args):bool{
    if($sender instanceof Player and $cmd->getName()=="fly"){
      $this->giaodienfly($sender);
      }
    return true;
  }

  public function giaodienfly(Player $player){
    $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
    $form = $api->createSimpleForm(function (Player $player, int $data = null){
            $result = $data;
            if($result === null){
                return true;
            }             
            switch($result){
                case 0:
                  if($player->hasPermission("fly.use")) {  
                    $this->flyMenu($player);
                    return true;
                  }else{
                    $player->sendMessage("§7【§eＦ§6Ｌ§cＹ§7】 §rBạn không quyền chức năng này");
                  }
                break;   
                case 1:
                  $this->buyFly($player);
                break;
                case 2:
                break;
            }
            });
            $coin = $this->coin->myCoin($player);
            $money = $this->money->myMoney($player);
            $form->setTitle("§l§c• §eGiao diện Mua Fly §l§c•");
            $form->setContent("§7[§e➠§7]§r Số Tiền của bạn: §e".$money." §7| §r Số Coin của bạn: §6".$coin." \n§c• §eVui lòng chọn §c•");
            $form->addButton("§aTính Năng Fly\n§7ấn vào để xem chi tiết",0,"textures/ui/fly");
            $form->addButton("§eMua§a Chức Năng Fly\n§7ấn vào để xem chi tiết",0,"textures/ui/shop");
            $form->addButton("§cThoát ra",0,"textures/ui/thoat");
            $form->sendToPlayer($player);
  }
  
  public function flyMenu(Player $player){ 
        $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
        $form = $api->createSimpleForm(function (Player $player, int $data = null){
            $result = $data;
            if($result === null){
                return true;
            }             
            switch($result){
                case 0:
                    $player->sendMessage("§7【§eＦ§6Ｌ§7cＹ§7】 §aĐã bật Fly!");
                    $player->setAllowFlight(true);
                break;
                    
                case 1:
                    $player->sendMessage("§7【§eＦ§6Ｌ§7cＹ§7】 §cĐã tắt Fly!");
                    $player->setAllowFlight(false);
                break;
                case 2:
                break;
            }
            
            
            });
            $times = $this->getTimeRank($player);
            $form->setTitle("§l§c• §eGiao diện FlyUI §l§c•");
            $form->setContent("Thời Gian của bạn còn: ".$times."\n§l§c• §eVui lòng chọn §l§c•");
            $form->addButton("§l§c• §eBật Fly §l§c•",0,"textures/ui/on");
            $form->addButton("§l§c• Tắt Fly §l§c•",0,"textures/ui/off");
            $form->addButton("§l§c• Thoát §l§c•",0,"textures/ui/thoat");
            $form->sendToPlayer($player);
            return $form;                                            
    }
  public function buyFly(Player $player){
    $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
    $form = $api->createSimpleForm(function (Player $player, int $data = null){
            $result = $data;
            if($result === null){
                return true;
            }             
            switch($result){
                case 0:
                  $this->flyTime($player);
                break;
                    
                case 1:
                  $this->flyFull($player);
                break;
                case 2:
                break;
            }
            });
            $coin = $this->coin->myCoin($player);
            $money = $this->money->myMoney($player);
            $form->setTitle("§l§c• §eGiao diện Mua Fly §l§c•");
            $form->setContent("§7[§e➠§7]§r Số Tiền của bạn: §e".$money." §7| §r Số Coin của bạn: §6".$coin." \n§l§c• §eVui lòng chọn §l§c•");
            $form->addButton("§aMua Fly Giờ\n§7ấn vào để xem chi tiết",0,"textures/ui/time");
            $form->addButton("§6Mua Fly Vĩnh Viễn\n§7ấn vào để xem chi tiết",0,"textures/ui/vinhvien");
            $form->addButton("§l§c• Thoát §l§c•",0,"textures/ui/thoat");
            $form->sendToPlayer($player);
  }
  public function flyTime(Player $sender){
      $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
       $form = $api->createCustomForm(function(Player $sender, array $data = null){
        $result = $data;
           if($result === null){
                $this->buyFly($sender);
                return;
            }
           if($data[1]== null)
           return false;
          if(!is_numeric($data[1])){
		  $sender->sendMessage("§7【§eＦ§6Ｌ§cＹ§7】§r Vui lòng Nhập Số Giờ Muốn mua");
		  return false;
	  }   
    if($sender->hasPermission("fly.use")) {
      $sender->sendMessage("§7【§eＦ§6Ｌ§cＹ§7】§r Bạn đã có chức năng Fly nên không thể mua nữa !");
    }else{
      $money = $this->money->myMoney($sender);
      if($money >= $data[1]*10000){
        $this->money->reduceMoney($sender, $data[1]*10000);
        $this->PurePerms()->getUserDataMgr()->setPermission($sender, "fly.use");
        $this->addTime($sender, $data[1]);
        $sender->sendMessage("§7【§eＦ§6Ｌ§cＹ§7】§r Bạn đã mua thành công Fly sử dụng trong §a".$data[1]."§r giờ");
      }else{
       $sender->sendMessage("§7【§eＦ§6Ｌ§cＹ§7】§r Bạn không đủ §eMoney §rđể mua Fly sử dụng trong§a ".$data[1]."§r giờ");
            }  
        }

       });
       $form->setTitle("§aMua §6Fly §aTheo Giờ ");
       $form->addLabel("§l§7Hãy nhập số giờ bạn muốn mua vào đây:");
       $form->addInput("§l§f§•[§c+§f]§r 10.000 Money = 1 giờ ", "0");
       $form->sendToPlayer($sender);
       return $form;
  }
  public function flyFull(Player $player){
    $api = $this->getServer()->getPluginManager()->getPlugin("FormAPI");
    $form = $api->createSimpleForm(function (Player $player, int $data = null){
            $result = $data;
            if($result === null){
                return true;
            }             
            switch($result){
                case 0:
                  $coin = $this->coin->myCoin($player);
                  if($coin >= 50){
                    $this->coin->reduceCoin($player, 5);
                    $this->PurePerms()->getUserDataMgr()->setPermission($player, "fly.use");
                    $player->sendMessage("§7【§eＦ§6Ｌ§cＹ§7】§r Bạn đã mua thành công Fly Vĩnh Viễn");
                  }
                break;
                    $player->sendMessage("§7【§eＦ§6Ｌ§cＹ§7】§r Bạn không đủ §a50 §6Coin để mua Fly vĩnh viển");
                case 1:
                break;

            }
            });
            $coin = $this->coin->myCoin($player);
            $money = $this->money->myMoney($player);
            $form->setTitle("§l§c• §eGiao diện Mua Fly §l§c•");
            $form->setContent("§7[§e➠§7]§r Số Tiền của bạn: §e".$money." §7| §r Số Coin của bạn: §6".$coin." \n§l§c• §e Bạn có muốn mua FLy Vĩnh Viễn không ? §l§c•");
            $form->addButton("Đồng ý",0,"textures/ui/yes");

            $form->addButton("Không",0,"textures/ui/no");
            $form->sendToPlayer($player);
  }
  
}
