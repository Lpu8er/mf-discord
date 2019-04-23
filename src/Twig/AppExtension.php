<?php
namespace App\Twig;

use App\Utils\Markdown;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

/**
 * Description of AppExtension
 *
 * @author lpu8er
 */
class AppExtension extends AbstractExtension {
    private $parser = null;
    
    public function __construct(Markdown $parser) {
        $this->parser = $parser;
    }
    
    public function getFilters() {
        return [
            new TwigFilter('md2html', [$this, 'markdownToHtml'], [
                'is_safe' => ['html'],
                'pre_escape' => 'html',
            ]),
        ];
    }

    public function markdownToHtml($content) {
        return $this->parser->toHtml($content);
    }
}
