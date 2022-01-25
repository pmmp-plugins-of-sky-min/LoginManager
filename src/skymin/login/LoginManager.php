<?php
declare(strict_types = 1);

namespace skymin\login;

use pocketmine\plugin\PluginBase;
use pocketmine\event\EventPriority;
use pocketmine\network\mcpe\JwtUtils;
use pocketmine\network\mcpe\protocol\LoginPacket;
use pocketmine\event\server\DataPacketReceiveEvent;

use function explode;
use function strtoupper;

final class LoginManager extends PluginBase{
	
	protected function onEnable() : void{
		$this->getServer()->getPluginManager()->registerEvent(DataPacketReceiveEvent::class, function(DataPacketReceiveEvent $ev) : void{
			$packet = $ev->getPacket();
			if($packet instanceof LoginPacket){
				$network = $ev->getOrigin();
				$clientData = JwtUtils::parse($packet->clientDataJwt)[1];
				if($clientData['DeviceOS'] === 1){
					$model = explode(" ", $clientData["DeviceModel"], 1)[0];
					if($model !== strtoupper($model)){
						$network->disconnect('툴박스가 감지되었습니다.');
						return;
					}
				}
				if($clientData['UIProfile'] === 1){
					$network->disconnect('UI 프로필을 클래식으로 설정후 다시 접속해주세요.');
					return;
				}
			}
		}, EventPriority::MONITOR, $this, true);
	}
	
}