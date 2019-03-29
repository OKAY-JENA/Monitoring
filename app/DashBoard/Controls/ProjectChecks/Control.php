<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\ProjectChecks;

class Control extends \Nette\Application\UI\Control
{

	/**
	 * @var \Pd\Monitoring\Project\Project
	 */
	private $project;

	/**
	 * @var \Pd\Monitoring\Check\ChecksRepository
	 */
	private $checksRepository;

	/**
	 * @var \Pd\Monitoring\DashBoard\Controls\Check\IFactory
	 */
	private $checkControlFactory;

	/**
	 * @var iterable
	 */
	private $checks;

	/**
	 * @var \Pd\Monitoring\User\User
	 */
	private $user;

	/**
	 * @var \Nextras\Orm\Relationships\OneHasMany
	 */
	private $userCheckNotifications;


	public function __construct(
		\Pd\Monitoring\Project\Project $project,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Pd\Monitoring\DashBoard\Controls\Check\IFactory $checkControlFactory,
		\Pd\Monitoring\User\User $user
	) {
		parent::__construct();
		$this->project = $project;
		$this->checksRepository = $checksRepository;
		$this->checkControlFactory = $checkControlFactory;
		$this->user = $user;
	}


	protected function attached($presenter)
	{
		parent::attached($presenter);

		$conditions = [
			'project' => $this->project->id,
		];
		$this->checks = $this->checksRepository->findBy($conditions)->orderBy('type');

		$this->userCheckNotifications = $this->user->userCheckNotifications;
	}

	protected function createTemplate()
	{
		$template = parent::createTemplate();

		$template->addFilter('tabColor', function (Tab $tab) {
			switch ($tab->getStatus()) {
				case \Pd\Monitoring\Check\ICheck::STATUS_OK:
					return 'bg-success';
				case \Pd\Monitoring\Check\ICheck::STATUS_ALERT:
					return 'bg-warning';
				case \Pd\Monitoring\Check\ICheck::STATUS_ERROR:
					return 'bg-danger';
				default:
					return 'bg-danger';
			}
		});

		return $template;
	}


	public function render(): void
	{
		/** @var array|Tab[] $tabs */
		$tabs = [];
		$checks = [];
		/** @var \Pd\Monitoring\Check\Check $check */
		foreach ($this->checks as $check) {
			if ( ! isset($tabs[$check->getType()]) || $check->status > $tabs[$check->getType()]->getStatus()) {
				$tabs[$check->getType()] = new Tab($check->getTitle(), $check->status);
			}
			$checks[$check->getType()][$check->id] = $check;
		}

		$this->template->add('tabs', $tabs);
		$this->template->add('checks', $checks);
		$this->template->add('project', $this->project);

		$this->template->setFile(__DIR__ . '/Control.latte')->render();
	}


	protected function createComponentCheck(): \Nette\Application\UI\Multiplier
	{
		$cb = function ($id) {
			$check = $this->checksRepository->getById($id);

			return $this->checkControlFactory->create($check, $this->userCheckNotifications->has([$this->user->id, $check->id]));
		};

		return new \Nette\Application\UI\Multiplier($cb);
	}
}
