<?php
namespace CM\Neos\Yubico\Command;

use CM\Neos\Yubico\Service\YubicoService;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Cli\CommandController;

/**
 * Yubico Command Controller
 */
class YubicoCommandController extends CommandController {

	/**
	 * @Flow\InjectConfiguration("api")
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