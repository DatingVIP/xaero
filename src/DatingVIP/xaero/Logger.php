<?php
namespace DatingVIP\xaero;

use DatingVIP\IRC\Robot,
    DatingVIP\IRC\Message;

class Logger implements \DatingVIP\IRC\Logger {
    public function onSend($line)    { printf("> %s\n", $line); }
	public function onReceive($line) { printf("< %s\n", $line); }
}
?>
