<?php
namespace CM\Neos\Yubico\Command;

use CM\Neos\Yubico\Service\YubicoService;
use Doctrine\ORM\Mapping as ORM;
use Neos\Flow\Annotations as Flow;
use Neos\Flow\Cli\CommandController;

/**
 * Yubico Command Controller
 */
class YubicoCommandController extends CommandController {

	/**
	 * @Flow\InjectConfiguration(package="CM.Neos.Yubico", path="api")
	 * @var array
	 */
	protected $apiSettings;

	/**
	 * @Flow\Inject
	 * @var YubicoService
	 */
	protected $yubicoService;

	/**
	 * @param string $otp
	 */
	public function testCommand($otp) {
		$this->outputLine($this->yubicoService->check($otp) ? 'Success' : 'Error');
	}

}