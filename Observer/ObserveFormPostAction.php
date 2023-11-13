<?php
/**
 * @category  Mage4
 * @package   Mage4_Turnstile
 * @author    Rehan Mobin <m.rehan.mobin@gmail.com>
 * @copyright Copyright (c) Mage4. All rights reserved. (https://www.mage4.com)
 */

namespace Mage4\Turnstile\Observer;

use Mage4\Turnstile\Model\Config;
use Mage4\Turnstile\Model\Turnstile\SiteverifyResponse;
use Magento\Framework\App\ActionInterface;
use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\Response\HttpInterface as HttpResponseInterface;
use Magento\Framework\Event\Observer;
use Magento\Framework\Event\ObserverInterface;
use Mage4\Turnstile\Model\Turnstile\TurnstileApi;
use Magento\Framework\App\Response\RedirectInterface;
use Psr\Log\LoggerInterface;
use Magento\Framework\App\ActionFlag;
use Magento\Framework\Message\ManagerInterface as MessageManagerInterface;

class ObserveFormPostAction implements ObserverInterface
{
    private Config $config;
    private TurnstileApi $turnstileApi;
    private LoggerInterface $logger;
    private MessageManagerInterface $messageManager;
    private RedirectInterface $redirect;
    private ActionFlag $actionFlag;
    private $actionNameToValidateTurnstile = [
        'contact_index_post', 'customer_account_createpost', 'customer_account_loginPost', 'customer_account_editPost', 'customer_account_forgotpasswordpost', 'sendfriend_product_sendmail', 'review_product_post', 'adminhtml_index_index', 'adminhtml_auth_forgotpassword'
    ];

    public function __construct(
        Config $config,
        TurnstileApi $turnstileApi,
        LoggerInterface $logger,
        MessageManagerInterface $messageManager,
        RedirectInterface $redirect,
        ActionFlag $actionFlag
    ) {
        $this->config = $config;
        $this->turnstileApi = $turnstileApi;
        $this->logger = $logger;
        $this->messageManager = $messageManager;
        $this->redirect = $redirect;
        $this->actionFlag = $actionFlag;
    }

    public function execute(Observer $observer): void
    {
        /** @var RequestInterface $request */
        $request = $observer->getEvent()->getData('request');
        /** @var ActionInterface $controller */
        $controller = $observer->getEvent()->getData('controller_action');
        if (false === $this->canActionValidate($request) || $controller->getRequest()->isPost() === false) {
            return;
        }
        if (empty($request->getParam('turnstile_for')) || false === $this->config->isEnabledFor($request->getParam('turnstile_for'))) {
            return;
        }
        /** @var HttpResponseInterface $actionResponse */
        $actionResponse = $controller->getResponse();
        $turnstileResponse = $request->getParam('cf-turnstile-response');

        if (empty($turnstileResponse)) {
            $this->processError($actionResponse, 'Turnstile verification failed.');
            return;
        }

        $this->turnstileApi->validateWidgetResponse($turnstileResponse)->unwrapOrElse(function (SiteverifyResponse $res) use ($actionResponse) {
            $this->processError($actionResponse, $res->errorMessage());
        });
    }

    private function canActionValidate(RequestInterface $request): bool
    {
        return \in_array($request->getFullActionName(), $this->actionNameToValidateTurnstile);
    }

    private function processError(HttpResponseInterface $response, string $errorMessage): void {
        $errorMessage = 'Couldflare Turnstile error: '. $errorMessage;
        $this->logger->error(__($errorMessage));
        $this->messageManager->addErrorMessage($errorMessage);
        $this->actionFlag->set('', ActionInterface::FLAG_NO_DISPATCH, true);
        $response->setRedirect($this->redirect->getRefererUrl());
    }
}
