<?php

declare(strict_types=1);

namespace App\Controllers\Dev;

use App\App\Routing\DevPaths;
use App\Application\Exercises\ExerciseSessionStore;
use App\Application\Http\Redirector;
use App\Application\Routing\UrlGenerator;
use App\Domain\Auth\UserContext;
use App\Domain\Exercise\ExerciseConfig;
use App\Domain\Exercise\ExerciseStep;
use App\Domain\Exercise\ExerciseType;

final class DevExerciseSessionController
{
    public function __construct(
        private readonly ExerciseSessionStore $sessionStore,
        private readonly Redirector $redirector,
        private readonly DevPaths $devPaths,
        private readonly UrlGenerator $urlGenerator
    ) {}

    /**
     * GET /dev/sesion-ejercicio
     * - Creates a session if it does not exist.
     * - Reads the current session from the session store.
     * - Renders a minimal debug page.
     */
    public function show(): void
    {
        $session = $this->sessionStore->getCurrentSession();

        if ($session === null) {
            $exerciseType = ExerciseType::howMuchDoYouKnowTopic();

            $userContext = new UserContext('dev-user', 'dev-opposition');

            $config = new ExerciseConfig(
                topicId: 0,
                difficulty: 2,
                flags: ['barajar_preguntas' => true]
            );

            $session = $this->sessionStore->create(
                exerciseType: $exerciseType,
                userContext: $userContext,
                config: $config,
                firstStep: ExerciseStep::first()
            );
        }

        $reloaded = $this->sessionStore->get($session->sessionId());
        if ($reloaded === null) {
            http_response_code(500);
            echo 'ERROR: Could not reload the session from the store.';
            return;
        }

        $this->renderHtml($reloaded);
    }

    /**
     * POST /dev/sesion-ejercicio/siguiente
     * - Loads the current session
     * - Advances to the next step
     * - Saves the session
     * - Redirects back (PRG)
     */
    public function next(): void
    {
        $session = $this->sessionStore->getCurrentSession();

        if ($session === null) {
            $this->redirector->redirect($this->devPaths->exerciseSessionBase());
            return;
        }

        $nextStep = $session->currentStep()->next() ?? $session->currentStep();
        $session->moveToStep($nextStep);

        $this->sessionStore->save($session);

        // PRG: avoid form resubmission
        $this->redirector->redirect($this->devPaths->exerciseSessionBase(), 303);
    }

    /**
     * POST /dev/sesion-ejercicio/reset
     * - Deletes the current session
     * - Redirects back
     */
    public function reset(): void
    {
        $currentSessionId = $this->sessionStore->getCurrentSessionId();
        if ($currentSessionId !== null) {
            $this->sessionStore->delete($currentSessionId);
        }

        $this->redirector->redirect($this->devPaths->exerciseSessionBase(), 303);
    }

    private function renderHtml(mixed $session): void
    {
        $sessionId = htmlspecialchars($session->sessionId(), ENT_QUOTES, 'UTF-8');
        $typeSlug = htmlspecialchars($session->exerciseType()->slug(), ENT_QUOTES, 'UTF-8');
        $typeName = htmlspecialchars($session->exerciseType()->name(), ENT_QUOTES, 'UTF-8');
        $step = htmlspecialchars($session->currentStep()->value, ENT_QUOTES, 'UTF-8');
        $createdAt = htmlspecialchars($session->createdAt()->format(\DateTimeInterface::ATOM), ENT_QUOTES, 'UTF-8');
        $updatedAt = htmlspecialchars($session->updatedAt()->format(\DateTimeInterface::ATOM), ENT_QUOTES, 'UTF-8');

        $config = $session->config();
        $topicId = $config->topicId();
        $difficulty = $config->difficulty();
        $flagsJson = htmlspecialchars(json_encode($config->flags(), JSON_PRETTY_PRINT), ENT_QUOTES, 'UTF-8');

        $nextAction = $this->urlGenerator->to($this->devPaths->exerciseSessionNext());
        $resetAction = $this->urlGenerator->to($this->devPaths->exerciseSessionReset());

        echo <<<HTML
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8"/>
  <title>Dev Exercise Session Smoke</title>
  <meta name="viewport" content="width=device-width, initial-scale=1"/>
  <style>
    body { font-family: Arial, sans-serif; padding: 16px; }
    code, pre { background: #f6f8fa; padding: 8px; border-radius: 8px; display: block; overflow:auto; }
    .row { display:flex; gap:12px; flex-wrap:wrap; }
    .card { border:1px solid #ddd; border-radius:12px; padding:12px; min-width: 280px; }
    form { display:inline-block; margin-right: 8px; }
    button { padding:8px 12px; }
  </style>
</head>
<body>
  <h1>Dev Exercise Session Smoke</h1>

  <div class="row">
    <div class="card">
      <h2>Session</h2>
      <p><strong>session id</strong>: <code>{$sessionId}</code></p>
      <p><strong>type</strong>: <code>{$typeSlug}</code> ({$typeName})</p>
      <p><strong>current step</strong>: <code>{$step}</code></p>
      <p><strong>created at</strong>: <code>{$createdAt}</code></p>
      <p><strong>updated at</strong>: <code>{$updatedAt}</code></p>
    </div>

    <div class="card">
      <h2>Config</h2>
      <p><strong>topic</strong>: <code>{$topicId}</code></p>
      <p><strong>difficulty</strong>: <code>{$difficulty}</code></p>
      <p><strong>flags</strong>:</p>
      <pre>{$flagsJson}</pre>
    </div>
  </div>

  <hr/>

  <form method="post" action="{$nextAction}">
    <button type="submit">Next step (POST → 303 → GET)</button>
  </form>

  <form method="post" action="{$resetAction}">
    <button type="submit">Reset session</button>
  </form>

  <p>
    ✅ Quick checks:
    <ul>
      <li>Refresh the page: the session should persist (no parameters in the URL).</li>
      <li>Click "Next step": the exercise step changes without leaving the page.</li>
      <li>Open DevTools → Network: sessionId should not appear in the URL.</li>
    </ul>
  </p>

</body>
</html>
HTML;
    }
}