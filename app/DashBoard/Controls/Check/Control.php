<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\Check;

class Control extends \Nette\Application\UI\Control
{

	/**
	 * @var \Pd\Monitoring\Check\Check
	 */
	private $check;

	/**
	 * @var \Pd\Monitoring\Check\ChecksRepository
	 */
	private $checksRepository;

	/**
	 * @var \Kdyby\RabbitMq\Connection
	 */
	private $rabbitConnection;

	/**
	 * @var \Pd\Monitoring\DashBoard\Controls\AliveChart\IFactory
	 */
	private $aliveChartControlFactory;

	/**
	 * @var \Pd\Monitoring\UserCheckNotifications\UserCheckNotificationsRepository
	 */
	private $userCheckNotificationsRepository;

	/**
	 * @var bool
	 */
	private $hasUserNotification;

	/**
	 * @var \Pd\Monitoring\User\User
	 */
	private $user;


	public function __construct(
		\Pd\Monitoring\Check\Check $check,
		\Pd\Monitoring\Check\ChecksRepository $checksRepository,
		\Kdyby\RabbitMq\Connection $rabbitConnection,
		\Pd\Monitoring\DashBoard\Controls\AliveChart\IFactory $aliveChartControlFactory,
		\Pd\Monitoring\UserCheckNotifications\UserCheckNotificationsRepository $userCheckNotificationsRepository,
		bool $hasUserNotification,
		\Pd\Monitoring\User\User $user
	) {
		parent::__construct();
		$this->check = $check;
		$this->checksRepository = $checksRepository;
		$this->rabbitConnection = $rabbitConnection;
		$this->aliveChartControlFactory = $aliveChartControlFactory;
		$this->userCheckNotificationsRepository = $userCheckNotificationsRepository;
		$this->hasUserNotification = $hasUserNotification;
		$this->user = $user;
	}


	protected function createTemplate()
	{
		/** @var \Latte\Runtime\Template $template */
		$template = parent::createTemplate();

		$template->addFilter('dateTime', function (\DateTimeImmutable $value) {
			return $value->format('j. n. Y H:i:s');
		});

		return $template;
	}


	public function render(): void
	{
		$this
			->template
			->setFile(__DIR__ . '/Control.latte')
			->add('check', $this->check)
			->add('hasUserNotification', $this->hasUserNotification)
			->render()
		;
	}


	public function handleDelete(): void
	{
		$this->checksRepository->removeAndFlush($this->check);
		$this->getPresenter()->flashMessage('Kontrola byla odebrána', \Pd\Monitoring\DashBoard\Presenters\BasePresenter::FLASH_MESSAGE_SUCCESS);
		$this->redirect('this');
	}


	public function handlePause(): void
	{
		$this->check->paused = ! $this->check->paused;
		$this->checksRepository->persistAndFlush($this->check);

		$this->processRequest();
	}


	public function handleRefresh(): void
	{
		$this->rabbitConnection->getProducer($this->check->getProducerName())->publish($this->check->id);

		$this->processRequest();
	}


	private function processRequest(): void
	{
		if ($this->getPresenter()->isAjax()) {
			$this->redrawControl();
		} else {
			$this->redirect('this');
		}
	}


	protected function createComponentAliveChart(): \Pd\Monitoring\DashBoard\Controls\AliveChart\Control
	{
		return $this->aliveChartControlFactory->create($this->check);
	}


	public function handleUserNotificationOn(): void
	{
		$notification = new \Pd\Monitoring\UserCheckNotifications\UserCheckNotifications();
		$notification->user = $this->user;
		$notification->check = $this->check;
		$this->userCheckNotificationsRepository->persistAndFlush($notification);
		$this->hasUserNotification = TRUE;

		$this->processRequest();
	}


	public function handleUserNotificationOff(): void
	{
		$this->userCheckNotificationsRepository->deleteUserCheckNotifications($this->user, $this->check);
		$this->hasUserNotification = FALSE;

		$this->processRequest();
	}

}
