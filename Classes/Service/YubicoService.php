<?php
namespace Axovis\Flow\Yubico\Service;

use Axovis\Flow\Yubico\Domain\Model\Key;
use Axovis\Flow\Yubico\Domain\Repository\KeyRepository;
use Doctrine\ORM\Mapping as ORM;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Log\SystemLoggerInterface;
use TYPO3\Flow\Persistence\QueryResultInterface;
use TYPO3\Flow\Security\Account;
use Yubikey\Validate;

/**
 * One Time Password (OTP) Service
 *
 * @Flow\Scope("singleton")
 */
class YubicoService {

	/**
	 * @Flow\InjectConfiguration("api")
	 * @var array
	 */
	protected $apiSettings;

	/**
	 * @Flow\Inject
	 * @var SystemLoggerInterface
	 */
	protected $systemLogger;

    /**
     * @Flow\Inject
     * @var KeyRepository
     */
    protected $keyRepository;

	/**
	 * @param string $token
	 * @return boolean
	 */
	public function check($token) {
        if(!isset($this->apiSettings['secretKey']) || !isset($this->apiSettings['clientId'])) {
            throw new \Exception('Yubico configuration missing! Make sure Axovis.Flow.Yubico.api.secretKey and Axovis.Flow.Yubico.api.clientId are defined in Settings.yaml.');
        }
        
		$validate = new Validate($this->apiSettings['secretKey'], $this->apiSettings['clientId']);
		$responses = $validate->check($token);
		return $responses->success();
	}

    /**
     * @param string $token
     * @param Account $account
     * @return boolean
     */
    public function checkForAccount($token, Account $account) {
        if(!$this->accountRequiresKey($account)) {
            return true;
        }

        if(!$this->check($token)) {
            return false;
        }

        return $this->keyMatchesAccount($token,$account);
    }

    /**
     * @param string $token
     * @return string
     */
    public function getPublicId($token) {
        if(is_string($token) && strlen($token) >= 12) {
            return substr($token, 0, 12);
        }
        
        return null;
    }

    /**
     * @param Account $account
     * @return boolean
     */
    public function accountRequiresKey(Account $account) {
        $keys = $this->getKeysForAccount($account);
        return $keys->count() > 0;
    }
    
    /**
     * @param string $token
     * @param Account $account
     * @return Key
     */
    public function keyMatchesAccount($token,Account $account) {
        $keys = $this->getKeysForAccount($account);
        /** @var Key $key */
        foreach($keys as $key) {
            if($key->getPublicId() == $this->getPublicId($token)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * @param Account $account
     * @return QueryResultInterface<Key>
     */
    public function getKeysForAccount(Account $account) {
        return $this->keyRepository->findByAccount($account);
    }
    
    /**
     * @param string $token
     * @return Key
     */
    public function getKeyForToken($token) {
        return $this->keyRepository->findByPublicId($this->getPublicId($token))->getFirst();
    }
}