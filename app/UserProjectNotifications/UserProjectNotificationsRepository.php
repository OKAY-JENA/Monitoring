<?php declare(strict_types = 1);

namespace Pd\Monitoring\UserProjectNotifications;

/**
 * @method UserProjectNotifications getBy(array $conds)
 */
class UserProjectNotificationsRepository extends \Nextras\Orm\Repository\Repository
{

	public static function getEntityClassNames(): array
	{
		return [
			UserProjectNotifications::class,
		];
	}


	public function checkIfUserHasSlackNotifications(\Pd\Monitoring\User\User $user, \Pd\Monitoring\Project\Project $project): bool
	{
		return (bool) $this->getBy(["user" => $user, "project" => $project]);
	}


	public function deleteUserProjectNotifications(\Pd\Monitoring\User\User $user, \Pd\Monitoring\Project\Project $project): void
	{
		$entity = $this->getBy(["user" => $user, "project" => $project]);
		$this->removeAndFlush($entity);
	}

}
