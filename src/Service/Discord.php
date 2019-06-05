<?php
namespace App\Service;

use App\Entity\MessageQueue;
use App\Utils\DiscordCommand as DiscordCommands;
use App\Utils\REST;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Psr\Log\LoggerInterface;
use Ratchet\Client\Connector as ClientConnector;
use Ratchet\Client\WebSocket;
use Ratchet\RFC6455\Messaging\MessageInterface;
use React\EventLoop\Factory;
use React\EventLoop\LoopInterface;
use React\Socket\Connector as ReactConnector;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Discord BOT service
 *
 * @author lpu8er
 */
class Discord {
    const OP_MESSAGE = 0;
    const OP_HEARTBEAT = 1;
    const OP_IDENTIFY = 2;
    const OP_HELLO = 10;
    const OP_RESUME = 6;
    const OP_RECONNECT = 7;
    const OP_INVALID_SESSION = 9;
    const OP_HEARTBEAT_ACK = 11;
    
    const EVENT_HEARTBEAT = 'HEARTBEAT';
    const EVENT_IDENTIFY = 'IDENTIFY';
    const EVENT_READY = 'READY';
    const EVENT_RESUME = 'RESUME';
    const EVENT_RESUMED = 'RESUMED';
    const EVENT_GUILD_CREATE = 'GUILD_CREATE'; // happens at first connections, may help to lay load stuff !
    const EVENT_TYPING_START = 'TYPING_START';
    const EVENT_MESSAGE_CREATE = 'MESSAGE_CREATE';
    const EVENT_MESSAGE_UPDATE = 'MESSAGE_UPDATE';
    const EVENT_MESSAGE_DELETE = 'MESSAGE_DELETE';
    const EVENT_PRESENCE_UPDATE = 'PRESENCE_UPDATE';
    const EVENT_PRESENCES_REPLACE = 'PRESENCES_REPLACE';
    const EVENT_MESSAGE_REACTION_ADD = 'MESSAGE_REACTION_ADD';
    const EVENT_CHANNEL_PINS_UPDATE = 'CHANNEL_PINS_UPDATE';
    const EVENT_GUILD_ROLE_UPDATE = 'GUILD_ROLE_UPDATE';
    const EVENT_CHANNEL_CREATE = 'CHANNEL_CREATE';
    const EVENT_CHANNEL_UPDATE = 'CHANNEL_UPDATE';
    const EVENT_GUILD_EMOJIS_UPDATE = 'GUILD_EMOJIS_UPDATE';
    const EVENT_VOICE_STATE_UPDATE = 'VOICE_STATE_UPDATE';
    const EVENT_GUILD_MEMBER_UPDATE = 'GUILD_MEMBER_UPDATE';
    
    const INTERVAL_MESSAGEQUEUES = 10;
    
    /**
     *
     * @var string
     */
    protected $uri;
    /**
     *
     * @var string
     */
    protected $scope;
    /**
     *
     * @var string
     */
    protected $token;
    /**
     *
     * @var string
     */
    protected $channel = null;
    
    /**
     *
     * @var array
     */
    protected $guilds = null;
    
    /**
     *
     * @var string
     */
    protected $gatewayUri = null;
    
    /**
     *
     * @var string
     */
    protected $prefix = null;
    
    /**
     *
     * @var array
     */
    protected $allowedCommands = [];
    
    /**
     *
     * @var array
     */
    protected $aliases = [];
    
    /**
     *
     * @var array 
     */
    protected $rolesCache = [];
    
    /**
     *
     * @var array
     */
    protected $rolesPerms = [];
    
    /**
     *
     * @var LoopInterface
     */
    protected $loop = null;
    
    protected $reactConnector = null;
    
    /**
     *
     * @var WebSocket
     */
    protected $ws = null;
    
    /**
     *
     * @var int
     */
    protected $lastSequence = null;
    
    /**
     *
     * @var int
     */
    protected $hbInterval = null;
    
    /**
     *
     * @var int
     */
    protected $sessionId = null;
    
    /**
     *
     * @var array
     */
    protected $flushableTalks = [];
    
    /**
     *
     * @var bool
     */
    protected $delayEnabled = false;
    
    /**
     *
     * @var EntityManagerInterface 
     */
    protected $em = null;
    
    /**
     *
     * @var LoggerInterface 
     */
    protected $logger = null;
    
    /**
     *
     * @var TranslatorInterface 
     */
    protected $translator = null;
    
    /**
     *
     * @var DateTime
     */
    protected $startDate = null;
    
    /**
     *
     * @var DateTime
     */
    protected $connectionDate = null;
    
    /**
     *
     * @var type 
     */
    protected $lastAck = true;
    
    /**
     *
     * @var type 
     */
    protected $me = null;
    
    /**
     *
     * @var type 
     */
    protected $guildId = null;
    
    /**
     *
     * @var type 
     */
    protected $lockChannel = null;
    
    /**
     *
     * @var array
     */
    protected $autoRoles = [];
    
    /**
     *
     * @var bool
     */
    protected $sayHello = false;
    
    /**
     *
     * @var bool
     */
    protected $saidHello = false;
    
    /**
     * 
     * @param string $uri
     * @param string $token
     * @param string $scope
     */
    public function __construct(EntityManagerInterface $em,
            LoggerInterface $logger,
            TranslatorInterface $translator,
            $uri, $token, $scope, $guildId, $channel, $prefix, $allowedCommands, $aliases, $lockChannel, $autoRoles, $sayHello) {
        $this->em = $em;
        $this->logger = $logger;
        $this->translator = $translator;
        $this->uri = $uri;
        $this->token = $token;
        $this->scope = $scope;
        $this->guildId = $guildId;
        $this->channel = $channel;
        $this->prefix = $prefix;
        $this->allowedCommands = $allowedCommands;
        $this->aliases = $aliases;
        $this->lockChannel = $lockChannel;
        $this->autoRoles = $autoRoles;
        $this->sayHello = !!$sayHello;
        $this->startDate = new DateTime;
    }
    
    /**
     * 
     * @return string
     */
    public function getPrefix(): string {
        return $this->prefix;
    }
    
    /**
     * 
     * @return string
     */
    public function getEscapedPrefix(): string {
        return preg_quote($this->prefix);
    }
    
    /**
     * 
     * @return DateTime
     */
    public function getStartDate(): DateTime {
        return $this->startDate;
    }
    
    /**
     * 
     * @return ?DateTime
     */
    public function getConnectionDate(): ?DateTime {
        return $this->connectionDate;
    }
    
    /**
     * 
     * @return array
     */
    public function getAutoRoles(): array {
        return $this->autoRoles;
    }
    
    /**
     * 
     * @return bool
     */
    public function isPolite(): bool {
        return $this->sayHello;
    }
    
    /**
     * 
     */
    public function getGuilds() {
        $this->guilds = [];
        $response = REST::json($this->uri, '/users/@me/guilds', null, [], [
            'Authorization' => 'Bot '.$this->token,
        ]);
        if($response->isValid()) {
            foreach($response->getContent() as $guild) {
                // grab emojis
                $sres = REST::json($this->uri, '/guilds/'.$guild['id'].'/emojis', null, [], [
                    'Authorization' => 'Bot '.$this->token,
                ]);
                $guild['emojis'] = $sres;
                $this->guilds[$guild['id']] = $guild;
            }
        }
    }
    
    /**
     * 
     */
    public function getGatewayUri() {
        $response = REST::json($this->uri, '/gateway/bot', null, [], [
            'Authorization' => 'Bot '.$this->token,
        ]);
        if($response->isValid()) {
            $d = $response->getContent();
            $this->gatewayUri = $d['url'];
        }
    }
    
    public function consoleLog($msg) {
        echo '['.date('Y-m-d H:i:s').'] '.$msg.PHP_EOL;
    }
    
    /**
     * 
     * @return $this
     */
    public function connect() {
        $this->getGuilds();
        $this->getGatewayUri();
        
        $this->loop = Factory::create();
        $this->reactConnector = new ReactConnector($this->loop, [
            'dns' => '8.8.8.8',
            'timeout' => 10
        ]);
        $connector = new ClientConnector($this->loop, $this->reactConnector);
        $connector($this->gatewayUri)->then([$this, 'onConnect'], [$this, 'onConnectError']);
        $this->loop->run();
    }
    
    /**
     * 
     */
    public function heartbeat() {
        if(!$this->lastAck) { $this->consoleLog('Did not received ACK on last heartbeat'); }
        $this->lastAck = false;
        $this->ws->send(json_encode(['op' => static::OP_HEARTBEAT, 'd' => $this->lastSequence]));
        $this->loop->addTimer($this->hbInterval, [$this, 'heartbeat']);
    }
    
    /**
     * 
     * @param mixed $msg
     * @param int $op
     * @param string $e
     * @param int $s
     */
    public function send($msg, $op, $e, $s = 0) {
        $this->ws->send(json_encode([
            'op' => $op,
            'd' => $msg,
            's' => $s,
            't' => $e,
        ]));
    }
    
    public function disconnect() {
        $this->ws->close();
    }
    
    public function kill() {
        $this->loop->stop();
    }
    
    /**
     * 
     * @param WebSocket $conn
     */
    public function onConnect(WebSocket $conn) {
        $this->connectionDate = new DateTime;
        $this->ws = $conn;
        $this->ws->on('message', [$this, 'onMessage']);
        $this->ws->on('close', [$this, 'onClose']);
    }
    
    /**
     * 
     * @param type $code
     * @param type $reason
     */
    public function onClose($code = null, $reason = null) {
        if($code > 1000) { // something gone wrong
            $this->consoleLog('Connection closed ('.$code.' - '.$reason.') - will resume');
            // try to restart it
            $this->connect()->resume();
        }
    }
    
    /**
     * 
     * @param MessageInterface $msg
     */
    public function onMessage(MessageInterface $msg) {
        $this->parseOperation($msg);
    }
    
    /**
     * 
     * @param Exception $e
     */
    public function onConnectError(Exception $e) {
        $this->consoleLog('Could not connect: '.$e->getMessage());
        $this->loop->stop();
    }
    
    /**
     * 
     * @param string $literal
     */
    protected function parseOperation($literal) {
        $js = json_decode($literal, true);
        $op = intval($js['op']);
        if(!empty($js['s'])) {
            $this->lastSequence = intval($js['s']);
        }
        switch($op) {
            case static::OP_HELLO:
                if(!empty($js['d']['heartbeat_interval'])) {
                    $this->hbInterval = max(1, floor(intval($js['d']['heartbeat_interval']) / 1000));
                    // start heartbeating
                    $this->loop->addTimer($this->hbInterval, [$this, 'heartbeat']);
                    // identify
                    $this->send([
                        'token' => $this->token,
                        'properties' => [
                            '$os' => 'linux',
                            '$browser' => 'cli',
                            '$device' => 'php',
                        ],
                    ], static::OP_IDENTIFY, static::EVENT_IDENTIFY);
                    $this->loop->addPeriodicTimer(static::INTERVAL_MESSAGEQUEUES, [$this, 'checkMessageQueue']);
                }
                break;
            case static::OP_HEARTBEAT_ACK:
                $this->lastAck = true;
                break;
            case static::OP_MESSAGE:
                $this->parseEvent($js['t'], $js['d']);
                break;
            case static::OP_RECONNECT:
                $this->consoleLog('Starting resume process by opcode reconnect received');
                $this->connect()->resume();
                break;
            default:
                $this->consoleLog('RECEIVED UNKNOWN OPCODE (trace below)');
                $this->consoleLog(var_export($js, true));
        }
    }
    
    /**
     * 
     * @param string $event
     * @param array $data
     */
    protected function parseEvent(string $event, $data) {
        if(static::EVENT_READY === $event) {
            $this->sessionId = $data['session_id']; // we shall keep it in a file or whatever
        } elseif(static::EVENT_GUILD_CREATE === $event) {
            
            if(empty($this->channel) && !empty($data['system_channel_id'])) { // use system channel to print base messages if none configured
                $this->channel = $data['system_channel_id'];
            }
            
            foreach($data['roles'] as $role) {
                $this->rolesCache[$role['id']] = $role['name'];
                $this->rolesPerms[$role['id']] = $role['permissions'];
            }
            
            $this->me = $this->me();
            
            if($this->isPolite() && !$this->saidHello) {
                $this->saidHello = true;
                $this->talk($this->t('Bot loaded and ready'));
            }
        } elseif(static::EVENT_MESSAGE_CREATE === $event) {
            $this->parseMessage($data);
        } elseif(static::EVENT_CHANNEL_CREATE === $event) {
            // discord re-send the channel_create event when the bot is replying, and does not send a different data payload
            // that's prolly a side-effect of their channel/message creation behaviour, so we gonna need to fix it some time later
        } elseif(static::EVENT_PRESENCE_UPDATE === $event) {
            // track username change to enforce server-side nickname
            $o = DiscordCommands\DiscordCommand::load('enforceNickname', [], $data, true);
            $o->execute($this);
        } elseif(in_array($event, [
            static::EVENT_TYPING_START,
            static::EVENT_MESSAGE_UPDATE,
            static::EVENT_MESSAGE_REACTION_ADD,
            static::EVENT_PRESENCES_REPLACE,
            static::EVENT_GUILD_ROLE_UPDATE,
            static::EVENT_CHANNEL_UPDATE,
            static::EVENT_GUILD_EMOJIS_UPDATE,
            static::EVENT_VOICE_STATE_UPDATE,
            static::EVENT_CHANNEL_PINS_UPDATE,
            static::EVENT_GUILD_MEMBER_UPDATE,])) { // ignored events (for now)
            
        } else { // monitored events
            $this->consoleLog('Received event "'.$event.'" (trace below)');
            $this->consoleLog(var_export($data, true));
        }
    }
    
    /**
     * 
     * @param array $data
     */
    protected function parseMessage($data) {
        // check if that's a bot message
        $matches = [];
        if(!$data['tts']
                && (0 == $data['type'])
                && preg_match('`^'.$this->getEscapedPrefix().'([a-zA-Z0-9]+)(( +)(.+))?$`', $data['content'], $matches)) { // 0 = message
            if(empty($data['guild_id'])) { // private message
                $this->parseCommand($matches[1], empty($matches[4])? []:explode(' ', $matches[4]), $data, true);
            } elseif(empty($this->lockChannel)
                    || ($data['channel_id'] === $this->lockChannel)) { // bot only listen some channels
                $this->parseCommand($matches[1], empty($matches[4])? []:explode(' ', $matches[4]), $data, false);
            }
        }
    }
    
    /**
     * 
     * @param string $cmd
     * @param array $args
     * @param array $pureData
     * @param bool $private
     */
    protected function parseCommand(string $cmd, array $args, array $pureData, bool $private = false) {
        if($this->isAllowedCommand($cmd)) {
            try {
                $cmd = $this->getAliasedCommand($cmd);
                $o = DiscordCommands\DiscordCommand::load($cmd, $args, $pureData);
                if(!empty($o)) {
                    $this->disableDelay();
                    $o->execute($this);
                    $this->disableDelay();
                } else {
                    $this->talk($this->t('Unimplemented command `%cmd%`', ['%cmd%' => $cmd,]), $pureData['channel_id']);
                }
            } catch (Exception $ex) {
                $this->logger->critical($ex->getMessage());
                $this->logger->critical($ex->getTraceAsString());
                $this->talk($this->t('An error occured, please retry later'), $pureData['channel_id']);
            }
        } else {
            $this->talk($this->t('Unrecognized command `%cmd%`', ['%cmd%' => $cmd,]), $pureData['channel_id']);
        }
    }
    
    /**
     * 
     * @param string $cmd
     * @return bool
     */
    public function isAllowedCommand(string $cmd): bool {
        return in_array($cmd, $this->allowedCommands);
    }
    
    /**
     * 
     * @return array
     */
    public function getAllowedCommands(): array {
        return $this->allowedCommands;
    }
    
    /**
     * 
     * @param string $actualComd
     * @return string
     */
    public function getAliasedCommand(string $actualComd): string {
        return array_key_exists($actualComd, $this->aliases)? ($this->aliases[$actualComd]):$actualComd;
    }
    
    /**
     * 
     * @return $this
     */
    public function enableDelay() {
        $this->delayEnabled = true;
        return $this;
    }
    
    /**
     * 
     * @return $this
     */
    public function disableDelay() {
        $this->delayEnabled = false;
        return $this;
    }
    
    /**
     * 
     * @param type $channelId
     */
    public function flush($channelId = null) {
        $this->disableDelay();
        $this->talk(implode(PHP_EOL, $this->flushableTalks), $channelId);
        $this->flushableTalks = [];
    }
    
    /**
     * 
     * @return array|null
     */
    public function me(): ?array {
        $response = REST::json($this->uri, '/users/@me', REST::METHOD_GET, [], [
            'Authorization' => 'Bot '.$this->token,
        ]);
        return $response->isValid()? $response->getContent():null;
    }
    
    /**
     * 
     * @param mixed $msg
     * @param ?string $channel
     * @param array $embeds uncompatible with delay atm
     */
    public function talk($msg, $channel = null, array $embeds = []) {
        if(empty($channel)) { $channel = $this->channel; }
        if($this->delayEnabled) {
            $this->flushableTalks[] = $msg;
        } else {
            $response = REST::json($this->uri, '/channels/'.$channel.'/messages', REST::METHOD_POST, [
                'content' => $msg,
                'embed' => $embeds,
            ], [
                'Authorization' => 'Bot '.$this->token,
            ]);
        }
    }
    
    /**
     * 
     * @param mixed $msg
     * @param ?string $url
     * @param array $fields
     * @param ?string $color in hexa
     * @param ?string $channel
     */
    public function embed($title, $url = null, array $fields = [], $color = null, $channel = null) {
        if(empty($channel)) { $channel = $this->channel; }
        $ctx = [
            'title' => $title,
        ];
        if(!empty($url)) {
            $ctx['url'] = $url;
        }
        if(!empty($color)) {
            $ctx['color'] = hexdec($color);
        }
        if(!empty($fields)) {
            $ctx['fields'] = $fields;
        }
        $response = REST::json($this->uri, '/channels/'.$channel.'/messages', REST::METHOD_POST, [
            'embed' => $ctx,
        ], [
            'Authorization' => 'Bot '.$this->token,
        ]);
    }
    
    /**
     * 
     */
    public function resume() {
        $this->consoleLog('Starting to resume');
        $response = $this->send([
            'token' => $this->token,
            'session_id' => $this->sessionId,
            'seq' => $this->lastSequence,
            ], static::OP_RESUME, static::EVENT_RESUME);
        $this->consoleLog('Response of ='.var_export($response, true));
    }
    
    /**
     * 
     * Check for message queue if any available message is there
     * 
     */
    public function checkMessageQueue() {
        $msg = $this->em->getRepository(MessageQueue::class)->findLastToRun();
        if(!empty($msg)) {
            $this->em->getRepository(MessageQueue::class)->flagAsProcessing($msg);
            $scmd = $msg->getMethod();
            $sargs = $msg->getArgs();
            $o = DiscordCommands\DiscordCommand::load($scmd, $sargs['args'], $sargs['data'], true);
            if(!empty($o)) {
                $this->disableDelay();
                $o->execute($this);
                $this->disableDelay();
            } else {
                $this->consoleLog('Unrecognized command from message queue : '.$scmd);
            }
            $this->em->getRepository(MessageQueue::class)->flagAsDone($msg);
        }
    }
    
    /**
     * 
     * @param string $name
     * @param bool $ignoreCache
     * @return ?string
     */
    public function getRoleId(string $name, bool $ignoreCache = false) {
        $returns = null;
        if(!$ignoreCache && in_array($name, $this->rolesCache)) {
            $returns = array_search($name, $this->rolesCache);
        } else {
            // @TODO
        }
        return $returns;
    }
    
    /**
     * 
     * @param string $discordUserId
     * @return array|null
     */
    public function getMember($discordUserId): ?array {
        $response = REST::json($this->uri, '/guilds/'.$this->guildId.'/members/'.$discordUserId, REST::METHOD_GET, [], [
            'Authorization' => 'Bot '.$this->token,
        ]);
        return $response->isValid()? $response->getContent():null;
    }
    
    /**
     * 
     * @param string $userId
     * @param string $roleId
     */
    public function removeRole($userId, $roleId) {
        REST::json($this->uri, '/guilds/'.$this->guildId.'/members/'.$userId.'/roles/'.$roleId, REST::METHOD_DELETE, [], [
            'Authorization' => 'Bot '.$this->token,
        ]);
    }
    
    /**
     * 
     * @param string $userId
     * @param string $roleId
     */
    public function addRole($userId, $roleId) {
        REST::json($this->uri, '/guilds/'.$this->guildId.'/members/'.$userId.'/roles/'.$roleId, REST::METHOD_PUT, [], [
            'Authorization' => 'Bot '.$this->token,
        ]);
    }
    
    /**
     * 
     * @param string $userId
     * @param string $newName
     */
    public function renameMember($userId, $newName) {
        REST::json($this->uri, '/guilds/'.$this->guildId.'/members/'.$userId, REST::METHOD_PATCH, [
            'nick' => $newName,
        ], [
            'Authorization' => 'Bot '.$this->token,
        ]);
    }
    
    /**
     * 
     * @param string $channelId
     */
    public function startTyping($channelId) {
        if(!empty($this->me)) {
            REST::json($this->uri, '/channels/'.$channelId.'/typing', REST::METHOD_POST, [], [
                'Authorization' => 'Bot '.$this->token,
            ]);
        }
    }
    
    /**
     * 
     * @return EntityManagerInterface
     */
    public function getEntityManager() {
        return $this->em;
    }
    
    /**
     * 
     * @return LoggerInterface
     */
    public function getLogger() {
        return $this->logger;
    }
    
    /**
     * Shortcut for translations
     * @param string $str
     * @param array $params
     * @return string
     */
    public function t(string $str, array $params = []): string {
        return $this->translator->trans($str, $params);
    }
}
