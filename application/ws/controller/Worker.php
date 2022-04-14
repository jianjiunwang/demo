<?php
namespace app\ws;

use think\worker\Server;

class Worker extends Server
{

	public function onMessage($connection,$data)
	{
		$connection->send(json_encode($data));
	}
}