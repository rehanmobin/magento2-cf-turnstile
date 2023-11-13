<?php
/**
 * @category  Mage4
 * @package   Mage4_Turnstile
 * @author    Rehan Mobin <m.rehan.mobin@gmail.com>
 * @copyright Copyright (c) Mage4. All rights reserved. (https://www.mage4.com)
 */

namespace Mage4\Turnstile\Model\Turnstile;

use Prewk\Result;

interface TurnstileApi
{
    public function validateWidgetResponse(string $widgetResponse): Result;
}
