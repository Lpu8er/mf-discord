<?php
namespace App\Utils;

use Parsedown;

/**
 * Description of Markdown
 *
 * @author lpu8er
 */
class Markdown {
    /**
     *
     * @var Parsedown
     */
    protected $parser = null;
    
    public function __construct() {
        $this->parser = new Parsedown();
    }
    
    /**
     * 
     * @param string $str
     * @return string
     */
    public function toHtml(string $str): string {
        return $this->parser->text($str);
    }
}
