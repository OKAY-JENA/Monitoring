<?php declare(strict_types = 1);

namespace Pd\Monitoring\Check;

/**
 * @method Check getById(int $id)
 * @method Check getBy(array $conds)
 * @method \Nextras\Orm\Collection\ICollection|Check[] findAll()
 * @method \Nextras\Orm\Collection\ICollection|Check[] findBy(array $conditions)
 */
class ChecksRepository extends \Nextras\Orm\Repository\Repository
{

	public static function getEntityClassNames(): array
	{
		return [
			Check::class,
			AliveCheck::class,
			DnsCheck::class,
			CertificateCheck::class,
			HttpStatusCodeCheck::class,
			FeedCheck::class,
			RabbitConsumerCheck::class,
			RabbitQueueCheck::class,
			NumberValueCheck::class,
			ErrorsCheck::class,
			XpathCheck::class,
		];
	}

	public function getEntityClassName(array $data): string
	{
		if ( ! isset($data['type'])) {
			return parent::getEntityClassName($data);
		} else {
			switch ($data['type']) {
				case ICheck::TYPE_ALIVE:
					return AliveCheck::class;
				case ICheck::TYPE_DNS:
					return DnsCheck::class;
				case ICheck::TYPE_CERTIFICATE:
					return CertificateCheck::class;
				case ICheck::TYPE_HTTP_STATUS_CODE:
					return HttpStatusCodeCheck::class;
				case ICheck::TYPE_FEED:
					return FeedCheck::class;
				case ICheck::TYPE_RABBIT_QUEUES:
					return RabbitQueueCheck::class;
				case ICheck::TYPE_RABBIT_CONSUMERS:
					return RabbitConsumerCheck::class;
				case ICheck::TYPE_NUMBER_VALUE:
					return NumberValueCheck::class;
				case ICheck::TYPE_XPATH:
					return XpathCheck::class;
				case ICheck::TYPE_ERRORS:
					return ErrorsCheck::class;

				default:
					throw new \Nextras\Orm\InvalidStateException();
			}
		}
	}
}
