<?php
namespace CM\Neos\Yubico\Service;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Log\SystemLoggerInterface;
use Yubikey\Validate;

/**
 * One Time Password (OTP) Service
 *
 * @Flow\Scope("singleton")
 */
class OneTimePasswordService {

	/**
	 * @Flow\InjectConfiguration(package="CM.Neos.Yubico", path="api")
	 * @var array
	 */
	protected $apiSettings;

	/**
	 * @Flow\Inject
	 * @var SystemLoggerInterface
	 */
	protected $systemLogger;

	/**
	 * @param string $token
	 * @return boolean
	 * @throws \Exception
	 */
	public function check($token) {
        if(!isset($this->apiSettings['secrectKey']) || !isset($this->apiSettings['clientId'])) {
            throw new \Exception('Yubico configuration missing! Make sure CM.Neos.Yubico.api.secretKey and CM.Neos.Yubico.api.clientId are defined in Settings.yaml.');
        }
        
		$validate = new Validate($this->apiSettings['secrectKey'], $this->apiSettings['clientId']);
		$responses = $validate->check($token);
		return $responses->success();
	}

}