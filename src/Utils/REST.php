<?php
namespace App\Utils;

/**
 * Description of REST
 *
 * @author lpu8er
 */
class REST {
    const METHOD_GET = 'GET';
    const METHOD_POST = 'POST';
    const METHOD_PATCH = 'PATCH';
    const METHOD_HEAD = 'HEAD';
    const METHOD_DELETE = 'DELETE';
    const METHOD_PUT = 'PUT';
    
    /**
     * 
     * @param string $base
     * @param string $uri
     * @param string $method
     * @param array $data
     * @param array $headers
     * @param array $options
     * @param bool $json
     * @return RESTResponse
     */
    public static function instant(string $base, string $uri, string $method = null, array $data = [], array $headers = [], array $options = [], bool $json = false) {
        $cls = get_called_class();
        $rest = new $cls($base);
        $rest->setUri($uri);
        if(!empty($method)) {
            $rest->setMethod($method);
        }
        foreach($data as $k => $v) {
            $rest->setData($k, $v);
        }
        foreach($headers as $k => $v) {
            $rest->addHeader($k, $v);
        }
        foreach($options as $k => $v) {
            $rest->setOption($k, $v);
        }
        if($json) {
            $rest->enableJson();
        } else {
            $rest->disableJson();
        }
        $returns = $rest->call();
        // flush
        unset($rest);
        return $returns;
    }
    
    /**
     * 
     * @param string $base
     * @param string $uri
     * @param string $method
     * @param array $data
     * @param array $headers
     * @param array $options
     * @return RESTResponse
     */
    public static function json(string $base, string $uri, string $method = null, array $data = [], array $headers = [], array $options = []) {
        return static::instant($base, $uri, $method, $data, $headers, $options, true);
    }
    
    /**
     *
     * @var type 
     */
    protected $baseUri = null;
    
    /**
     *
     * @var string 
     */
    protected $uri = null;
    
    /**
     *
     * @var string 
     */
    protected $method = 'GET';
    
    /**
     *
     * @var array 
     */
    protected $data = [];
    
    /**
     *
     * @var array 
     */
    protected $headers = [];
    
    /**
     *
     * @var array 
     */
    protected $options = [];
    
    /**
     *
     * @var type 
     */
    protected $jsonSend = false;
    
    /**
     *
     * @var type 
     */
    protected $jsonReceive = false;
    
    /**
     *
     * @var RESTResponse
     */
    protected $response = null;
    
    
    public function __construct(string $baseUri) {
        $this->baseUri = $baseUri;
        // setup some default options
        $this->setOption(CURLOPT_FOLLOWLOCATION, true);
        $this->setOption(CURLOPT_FAILONERROR, false);
        $this->setOption(CURLOPT_RETURNTRANSFER, true);
    }
    
    public function setUri(string $uri): self {
        $this->uri = $uri;
        return $this;
    }
    
    public function setMethod(string $method): self {
        $this->method = $method;
        return $this;
    }
    
    public function clearData(): self {
        $this->data = [];
        return $this;
    }
    
    public function setData(string $k, $v): self {
        $this->data[$k] = $v;
        return $this;
    }
    
    public function addHeader(string $k, $v): self {
        $this->headers[$k] = $v;
        return $this;
    }
    
    public function hasHeader(string $k): bool {
        return array_key_exists($k, $this->headers);
    }
    
    public function clearHeaders(): self {
        $this->headers = [];
        return $this;
    }
    
    public function clearOptions(): self {
        $this->options = [];
        return $this;
    }
    
    public function setOption($k, $v): self {
        $this->options[$k] = $v;
        return $this;
    }
    
    public function hasOption(string $k): bool {
        return array_key_exists($k, $this->options);
    }
    
    public function enableJson(): self {
        return $this->enableJsonReceive()->enableJsonSend();
    }
    
    public function disableJson(): self {
        return $this->disableJsonReceive()->disableJsonSend();
    }
    
    public function enableJsonSend(): self {
        $this->jsonSend = true;
        return $this;
    }
    
    public function enableJsonReceive(): self {
        $this->jsonReceive = true;
        return $this;
    }
    
    public function disableJsonSend(): self {
        $this->jsonSend = false;
        return $this;
    }
    
    public function disableJsonReceive(): self {
        $this->jsonReceive = false;
        return $this;
    }
    
    public function call() {
        $this->response = new RESTResponse();;
        
        $ch = curl_init();
        // uri and data prepare stuff depending on method
        $uri = rtrim($this->baseUri, '/').'/'.ltrim($this->uri, '/');
        if(in_array($this->method, [static::METHOD_GET, static::METHOD_HEAD,])) {
            if(!empty($this->data)) {
                $uri .= '?'.http_build_query($this->data);
            }
        } else {
            if(in_array($this->method, [static::METHOD_POST,])) {
                curl_setopt($ch, CURLOPT_POST, true);
            } else {
                curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $this->method);
            }
            if($this->jsonSend && !$this->hasHeader('Content-Type')) {
                $this->addHeader('Content-Type', 'application/json'); // prepare
                curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($this->data));
            } elseif(!empty($this->data)) {
                curl_setopt($ch, CURLOPT_POSTFIELDS, $this->data);
            }
        }
        curl_setopt($ch, CURLOPT_URL, $uri);
        // headers
        if(!empty($this->headers)) {
            $shs = [];
            foreach($this->headers as $k => $v) {
                $shs[] = $k.': '.(is_scalar($v)? $v:implode(' ', $v));
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $shs);
        }
        // response headers
        curl_setopt($ch, CURLOPT_HEADERFUNCTION, [$this, 'storeResponseHeader']);
        // options
        foreach($this->options as $k => $v) {
            curl_setopt($ch, $k, $v);
        }
        // here we go
        $res = curl_exec($ch);
        $infos = curl_getinfo($ch);
        $this->response->setCode($infos['http_code']);
        if($this->jsonReceive || (false !== strpos('json', $this->response->getHeader('Content-Type', '')))) {
            $this->response->setContent(json_decode($res, true));
        } else {
            $this->response->setContent($res);
        }
        if(!$this->response->isValid()) { // append full body in error if not json
            if(!$this->jsonReceive) {
                $this->response->error($res);
            } else { // manage in another way ?
                
            }
        }
        return $this->response;
    }
    
    /**
     * internal to handle each response headers
     * @param resource $ch
     * @param string $headerline
     * @return int
     */
    protected function storeResponseHeader($ch, $headerline) {
        $this->response->addHeader(trim($headerline));
        return strlen($headerline);
    }
}
