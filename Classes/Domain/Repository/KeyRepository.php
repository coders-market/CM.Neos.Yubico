<?php
namespace CM\Neos\Yubico\Domain\Repository;

use Neos\Flow\Annotations as Flow;
use Neos\Flow\Persistence\Doctrine\Repository;
use Neos\Flow\Persistence\QueryResultInterface;
use Neos\Flow\Security\Account;

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
	 * @return object
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