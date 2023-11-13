<?php
/**
 * @category  Mage4
 * @package   Mage4_Turnstile
 * @author    Rehan Mobin <m.rehan.mobin@gmail.com>
 * @copyright Copyright (c) Mage4. All rights reserved. (https://www.mage4.com)
 */

namespace Mage4\Turnstile\Block;

use Magento\Framework\Exception\InputException;
use Magento\Framework\Serialize\Serializer\Json;
use Magento\Framework\View\Element\Template;
use Mage4\Turnstile\Model\Config;

class Turnstile extends Template
{
    private Json $serializer;
    private Config $config;

    public function __construct(
        Template\Context $context,
        Json $serializer,
        Config $config,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->serializer = $serializer;
        $this->config = $config;
    }

    public function getTurnstileId(): string
    {
        return \sha1('cf.turnstile-' . $this->getData('turnstile_for'));
    }

    /**
     * {@inheritdoc}
     *
     * @return string
     * @throws InputException
     */
    public function getJsLayout()
    {
        $layout = $this->serializer->unserialize(parent::getJsLayout());
        $action = \explode('/', $this->getData('turnstile_for'));
        $action = \end($action);
        if (isset($layout['components']['turnstile'])) {
            $layout['components'][$this->getTurnstileId()] = $layout['components']['turnstile'];
            unset($layout['components']['turnstile']);
        }

        $layout['components'][$this->getTurnstileId()] = array_replace_recursive(
            [
                'settings' => \array_merge($this->config->getUiConfig(), ['action' => $action]),
            ],
            $layout['components'][$this->getTurnstileId()]
        );
        $layout['components'][$this->getTurnstileId()]['turnstileId'] = $this->getTurnstileId();

        return $this->serializer->serialize($layout);
    }

    /**
     * @return string
     * @throws InputException
     */
    public function toHtml()
    {
        $key = $this->getData('turnstile_for');
        if (empty($key) || !$this->config->isEnabledFor($key)) {
            return '';
        }

        return parent::toHtml();
    }
}
