<?php
namespace Axovis\Flow\Yubico\Domain\Repository;

use Doctrine\ORM\Mapping as ORM;
use Axovis\Flow\Yubico\Domain\Model\Key;
use TYPO3\Flow\Annotations as Flow;
use TYPO3\Flow\Persistence\Doctrine\Repository;
use TYPO3\Flow\Persistence\QueryResultInterface;
use TYPO3\Flow\Security\Account;

/**
 * Key Repository
 *
 * @Flow\Scope("singleton")
 */
class KeyRepository extends Repository {

	/**
	 * @param Account $account
	 * @return QueryResultInterface
	 */
	public function findByAccount(Account $account) {
		$query = $this->createQuery();

		$query->matching($query->equals('account', $account));

		return $query->execute();
	}

    /**
     * @param string $publicId
     * @return QueryResultInterface
     */
    public function findByPublicId($publicId) {
        $query = $this->createQuery();

        $query->matching($query->equals('publicId', $publicId));

        return $query->execute();
    }

	/**
	 * @param Account $account
	 * @param string $publicId
	 * @return Key
	 */
	public function findOneByAccountAndPublicId(Account $account, $publicId) {
		$query = $this->createQuery();

		$query->matching($query->logicalAnd(
			$query->equals('account', $account),
			$query->equals('publicId', $publicId)
		));

		return $query->execute()->getFirst();
	}

}