<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\AddEditCheck;

class Factory
{

	private \Pd\Monitoring\Check\ChecksRepository $checksRepository;

	private \Pd\Monitoring\DashBoard\Forms\Factory $formFactory;

	private \Pd\Monitoring\Project\ProjectsRepository $projectsRepository;


	public function __construct(
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Pd\Monitoring\DashBoard\Forms\Factory $formFactory,
		\Pd\Monitoring\Project\ProjectsRepository $projectsRepository
	)
	{
		$this->checksRepository = $checksRepository;
		$this->formFactory = $formFactory;
		$this->projectsRepository = $projectsRepository;
	}


	public function create(\Pd\Monitoring\Project\Project $project, int $type, ?\Pd\Monitoring\Check\Check $check = NULL): Control
	{
		switch ($type) {
			case \Pd\Monitoring\Check\ICheck::TYPE_ALIVE:
				$processor = new AliveCheckProcessor();
				break;

			case \Pd\Monitoring\Check\ICheck::TYPE_DNS:
				$processor = new DnsCheckProcessor();
				break;

			case \Pd\Monitoring\Check\ICheck::TYPE_CERTIFICATE:
				$processor = new CertificateCheckProcessor();
				break;

			case \Pd\Monitoring\Check\ICheck::TYPE_HTTP_STATUS_CODE:
				$processor = new HttpStatusCodeCheckProcessor();
				break;

			case \Pd\Monitoring\Check\ICheck::TYPE_FEED:
				$processor = new FeedCheckProcessor();
				break;

			case \Pd\Monitoring\Check\ICheck::TYPE_RABBIT_QUEUES:
				$processor = new RabbitQueueCheckProcessor();
				break;

			case \Pd\Monitoring\Check\ICheck::TYPE_RABBIT_CONSUMERS:
				$processor = new RabbitConsumerCheckProcessor();
				break;

			case \Pd\Monitoring\Check\ICheck::TYPE_NUMBER_VALUE:
				$processor = new NumberValueCheckProcessor();
				break;

			case \Pd\Monitoring\Check\ICheck::TYPE_XPATH:
				$processor = new XpathCheckProcessor();
				break;

			case \Pd\Monitoring\Check\ICheck::TYPE_ERRORS:
				$processor = new ErrorsCheckProcessor();
				break;

			default:
				throw new \InvalidArgumentException();
				break;
		}

		$control = new Control($project, $check, $processor, $this->formFactory, $this->checksRepository, $this->projectsRepository);

		return $control;
	}

}
