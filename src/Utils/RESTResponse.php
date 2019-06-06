<?php
namespace App\Utils;

/**
 * Description of RESTResponse
 *
 * @author lpu8er
 */
class RESTResponse {
    /**
     * 
     * @param int $code
     * @param mixed $response
     * @param array $errors
     * @return RESTResponse
     */
    public static function factory(int $code, $response, array $errors = []): self {
        $returns = (new RESTResponse)->setCode($code)->setContent($response);
        foreach($errors as $e) {
            $this->error($e);
        }
        return $returns;
    }
    
    /**
     *
     * @var integer
     */
    protected $code;
    
    /**
     *
     * @var array 
     */
    protected $errors = [];
    
    /**
     *
     * @var array 
     */
    protected $headers = [];
    
    /**
     *
     * @var mixed 
     */
    protected $content = null;
    
    /**
     * 
     * @param int $code
     * @return $this
     */
    public function setCode(int $code): self {
        $this->code = $code;
        return $this;
    }
    
    /**
     * 
     * @return $this
     */
    public function clearErrors(): self {
        $this->errors = [];
        return $this;
    }
    
    /**
     * 
     * @return $this
     */
    public function clearHeaders(): self {
        $this->headers = [];
        return $this;
    }
    
    /**
     * 
     * @param mixed $error
     * @return $this
     */
    public function error($error): self {
        $this->errors[] = $error;
        return $this;
    }
    
    /**
     * 
     * @param string $header
     * @return $this
     */
    public function addHeader(string $header): self {
        $x = explode(':', $header);
        if(1 < count($x)) {
            $k = array_shift($x);
            if(1 === count($x)) {
                $this->addParsedHeader($k, array_shift($x));
            } else {
                $this->addParsedHeader($k, $x);
            }
        } else { // full stack header ?
            $this->addParsedHeader($header, $header);
        }
        return $this;
    }
    
    /**
     * 
     * @param string $k
     * @param mixed $v
     * @return $this
     */
    protected function addParsedHeader(string $k, $v): self {
        // trim
        if(is_scalar($v)) { $v = trim($v); }
        elseif(is_array($v)) { $v = array_map('trim', $v); }
        // here we go
        if(array_key_exists($k, $this->headers)) {
            if(is_array($v) && is_array($this->headers[$k])) {
                $this->headers[$k] = array_merge($this->headers[$k], $v);
            } elseif(is_array($this->headers[$k])) {
                $this->headers[$k][] = $v;
            } else {
                $this->headers[$k] = $v;
            }
        } else {
            $this->headers[$k] = $v;
        }
        return $this;
    }
    
    /**
     * 
     * @param mixed $content
     * @return $this
     */
    public function setContent($content): self {
        $this->content = $content;
        return $this;
    }
    
    /**
     * 
     * @return integer
     */
    public function getCode() {
        return $this->code;
    }
    
    /**
     * 
     * @return bool
     */
    public function isValid(): bool {
        return (200 <= $this->code) && (300 > $this->code);
    }
    
    /**
     * 
     * @return array
     */
    public function getErrors() {
        return $this->errors;
    }
    
    /**
     * 
     * @return boolean
     */
    public function hasErrors(): bool {
        return !empty($this->errors);
    }
    
    /**
     * 
     * @return array
     */
    public function getHeaders() {
        return $this->headers;
    }

    /**
     * 
     * @return mixed
     */
    public function getContent() {
        return $this->content;
    }
    
    /**
     * 
     * @param string $k
     * @return bool
     */
    public function hasHeader(string $k): bool {
        return array_key_exists($k, $this->headers);
    }
    
    /**
     * 
     * @param string $k
     * @param mixed $def
     * @return mixed
     */
    public function getHeader(string $k, $def = null) {
        return $this->hasHeader($k)? $this->headers[$k]:$def;
    }
}
