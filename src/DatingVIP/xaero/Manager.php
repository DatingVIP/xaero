<?php
namespace DatingVIP\xaero;

use DatingVIP\IRC\Message;

class Manager implements \DatingVIP\IRC\Manager {

    public function onStartup(\DatingVIP\IRC\Robot $robot) {
		printf("startup\n");
	}

	public function onJoin  (\DatingVIP\IRC\Robot $robot, Message $message) {}
	public function onNick  (\DatingVIP\IRC\Robot $robot, Message $message) {}
	public function onPart  (\DatingVIP\IRC\Robot $robot, Message $message) {}
	public function onPriv  (\DatingVIP\IRC\Robot $robot, Message $message) {}

	public function onShutdown(\DatingVIP\IRC\Robot $robot) {
		printf("shutdown\n");
	}
}
?>
