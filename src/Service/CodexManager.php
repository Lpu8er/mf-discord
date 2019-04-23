<?php
namespace App\Service;

use Symfony\Component\Finder\Finder;

/**
 * Description of CodexManager
 *
 * @author lpu8er
 */
class CodexManager {
    
    /**
     *
     * @var Finder 
     */
    protected $finder = null;
    
    public function __construct(string $projectDir, string $uriCodex) {
        $this->finder = (new Finder)
                ->depth('== 0')
                ->in($projectDir.$uriCodex)
                ->sortByModifiedTime();
    }
    
    /**
     * 
     * @param bool $ignoreCache
     * @return array
     */
    public function list(bool $ignoreCache = false): array {
        $returns = [];
        foreach($finder->files() as $f) {
            $returns[] = $f;
        }
        return $returns;
    }
    
    /**
     * 
     * @param string $name
     * @return string
     */
    protected function nameToFile(string $name): string {
        return preg_replace('`[^a-zA-Z0-9-]`', '', $name).'.md';
    }
    
    /**
     * 
     * @param string $name without the md ext
     * @return bool
     */
    public function exists(string $name): bool {
        return (1 == $this->finder->name($this->nameToFile($name))->hasResults());
    }
    
    /**
     * 
     * @param string $name
     * @return string|null
     */
    public function get(string $name): ?string {
        $frst = null;
        $fnd = $this->finder->name($this->nameToFile($name))->files();
        if(!empty($fnd)) {
            $frst = array_values(iterator_to_array($fnd))[0];
        }
        return empty($frst)? null:($frst->getContents());
    }
}
