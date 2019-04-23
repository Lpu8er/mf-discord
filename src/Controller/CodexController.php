<?php
namespace App\Controller;

use Symfony\Component\Routing\Annotation\Route;

/**
 * Description of CodexController
 *
 * @author lpu8er
 * @Route("/codex")
 */
class CodexController extends BaseController {
    /**
     * @Route("/{slug}", name="codex", methods="GET", requirements={"slug"="[a-zA-Z0-9-]+"})
     */
    public function executeIndex($slug) {
        // does that file exists ?
        
    }
}
