<?php
namespace Axovis\Flow\Yubico\Authentication\Provider;

use Axovis\Flow\Yubico\Authentication\Token\UsernamePassword;
use Axovis\Flow\Yubico\Domain\Model\Key;
use Axovis\Flow\Yubico\Domain\Repository\KeyRepository;
use Axovis\Flow\Yubico\Service\YubicoService;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Log\SystemLoggerInterface;
use TYPO3\Flow\Persistence\PersistenceManagerInterface;
use TYPO3\Flow\Security\Account;
use TYPO3\Flow\Security\Authentication\TokenInterface;
use TYPO3\Flow\Security\Exception\UnsupportedAuthenticationTokenException;

/**
 * An authentication provider that authenticates
 * TYPO3\Flow\Security\Authentication\Token\UsernamePassword tokens.
 * The accounts are stored in the Content Repository.
 */
class PersistedUsernamePasswordProvider extends \TYPO3\Flow\Security\Authentication\Provider\PersistedUsernamePasswordProvider {

	/**
	 * @Flow\Inject
	 * @var KeyRepository
	 */
	protected $keyRepository;

	/**
	 * @Flow\Inject
	 * @var YubicoService
	 */
	protected $yubicoService;

	/**
	 * @Flow\Inject
	 * @var PersistenceManagerInterface
	 */
	protected $persistenceManager;

    /**
     * @Flow\Inject
     * @var SystemLoggerInterface
     */
    protected $systemLogger;

	/**
	 * Returns the class names of the tokens this provider can authenticate.
	 *
	 * @return array
	 */
	public function getTokenClassNames() {
		return array('Axovis\Flow\Yubico\Authentication\Token\UsernamePassword');
	}

	/**
	 * Checks the given token for validity and sets the token authentication status
	 * accordingly (success, wrong credentials or no credentials given).
	 *
	 * @param \TYPO3\Flow\Security\Authentication\TokenInterface $authenticationToken The token to be authenticated
	 * @return void
	 * @throws \TYPO3\Flow\Security\Exception\UnsupportedAuthenticationTokenException
	 */
	public function authenticate(TokenInterface $authenticationToken) {
		if (!($authenticationToken instanceof UsernamePassword)) {
			throw new UnsupportedAuthenticationTokenException('This provider cannot authenticate the given token.', 1217339840);
		}
		/** @var $account \TYPO3\Flow\Security\Account */
		$account = NULL;
		$credentials = $authenticationToken->getCredentials();

		if (is_array($credentials) && isset($credentials['username'])) {
			$providerName = $this->name;
			$accountRepository = $this->accountRepository;
			$this->securityContext->withoutAuthorizationChecks(function() use ($credentials, $providerName, $accountRepository, &$account) {
				$account = $accountRepository->findActiveByAccountIdentifierAndAuthenticationProviderName($credentials['username'], $providerName);
			});
		}

		if (is_object($account)) {
			$hasKey = $this->yubicoService->accountRequiresKey($account);

			$currentKey = NULL;
			$token = trim($credentials['token']);
			$publicId = $this->yubicoService->getPublicId($token);
			if ($hasKey === FALSE && $publicId !== NULL) {
				$currentKey = $this->createKey($publicId, $account);
			} elseif ($hasKey === TRUE && $publicId !== NULL) {
				$currentKey = $this->keyRepository->findOneByAccountAndPublicId($account, $publicId);
				if ($currentKey === NULL || !$currentKey->matchPublicId($publicId)) {
					$authenticationToken->setAuthenticationStatus(TokenInterface::WRONG_CREDENTIALS);
					return;
				}
			} elseif ($hasKey === TRUE && $publicId === NULL) {
				$authenticationToken->setAuthenticationStatus(TokenInterface::WRONG_CREDENTIALS);
				return;
			}

			if ($this->hashService->validatePassword($credentials['password'], $account->getCredentialsSource()) && $this->validateOneTimePassword($token, $currentKey)) {
				$authenticationToken->setAuthenticationStatus(TokenInterface::AUTHENTICATION_SUCCESSFUL);
				$authenticationToken->setAccount($account);
				if ($currentKey instanceof Key && $this->persistenceManager->isNewObject($currentKey)) {
					$this->keyRepository->add($currentKey);
				}
			} else {
				$authenticationToken->setAuthenticationStatus(TokenInterface::WRONG_CREDENTIALS);
			}
		} elseif ($authenticationToken->getAuthenticationStatus() !== TokenInterface::AUTHENTICATION_SUCCESSFUL) {
			$authenticationToken->setAuthenticationStatus(TokenInterface::NO_CREDENTIALS_GIVEN);
		}
	}

	/**
	 * Create the key for the given account
	 *
	 * @param string $publicId
	 * @param Account $account
	 * @return Key
	 */
	protected function createKey($publicId, Account $account) {
		$key = new Key($publicId, $account);
		return $key;
	}

	/**
	 * Validate One Time Password
	 *
	 * We can skip the validation is the current user doesn't have a valid Key
	 *
	 * @param string $token
	 * @param string $currentKey
	 * @return bool
	 */
	protected function validateOneTimePassword($token, $currentKey) {
		if ($currentKey === NULL) {
			return TRUE;
		}
		return $this->yubicoService->check($token);
	}

}
