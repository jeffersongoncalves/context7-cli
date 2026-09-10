<?php

namespace App\Exceptions;

use JeffersonGoncalves\LaravelZero\ApiClient\ApiException;

/**
 * Context7 reports errors as { "error": "...", "message": "..." }, which the
 * base ApiException::extractMessage() already understands, so no override is
 * needed here — this class only binds the shared handling to a concrete type.
 */
class Context7ApiException extends ApiException {}
