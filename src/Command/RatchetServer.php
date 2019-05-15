<?php

namespace App\Command;

use App\Service\RatchetSocket;
use Ratchet\Http\HttpServer;
use Ratchet\Server\IoServer;
use Ratchet\WebSocket\WsServer;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Description of RatchetServer
 *
 * @author lpu8er
 */
class RatchetServer extends Command {

    protected $socketService = null;
    
    protected $discordService = null;
    
    public function __construct(RatchetSocket $socketService, \App\Service\Discord $discordService) {
        $this->socketService = $socketService;
        $this->discordService = $discordService;
        parent::__construct();
    }

    protected function configure() {
        $this->setName('ratchet:start');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $ws = new WsServer($this->socketService);
        
        IoServer::factory(
                new HttpServer($ws),
                8080
        )->run();
    }

}
