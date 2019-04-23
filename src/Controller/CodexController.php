<?php
namespace App\Controller;

use App\Service\CodexManager;
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
    public function executeIndex(CodexManager $codexManager, $slug) {
        $returns = null;
        // does that file exists ?
        if($codexManager->exists($slug)) {
            $returns = $this->render('codex/classic.html.twig', [
                'content' => $codexManager->get($slug),
            ]);
        } else {
            $returns = $this->createNotFoundException();
        }
        return $returns;
    }
}
