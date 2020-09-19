<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Presenters;

abstract class BasePresenter extends \Nette\Application\UI\Presenter
{

	use \Pd\Monitoring\DashBoard\TSecuredComponent;
	use \Pd\Monitoring\DashBoard\Controls\LastRefresh\TFactory;
	use \Pd\Monitoring\DashBoard\Controls\Settings\TFactory;
	use \Pd\Monitoring\DashBoard\Controls\Favicons\TFactory;

	public const FLASH_MESSAGE_SUCCESS = 'success';
	public const FLASH_MESSAGE_INFO = 'info';
	public const FLASH_MESSAGE_WARNING = 'warning';
	public const FLASH_MESSAGE_ERROR = 'danger';

	/**
	 * @var \Pd\Monitoring\DashBoard\Controls\Logout\IFactory
	 */
	private $logoutControlFactory;


	public function injectServices(
		\Pd\Monitoring\DashBoard\Controls\Logout\IFactory $logoutControlFactory
	) {
		$this->logoutControlFactory = $logoutControlFactory;
	}


	protected function createComponentLogout(): \Pd\Monitoring\DashBoard\Controls\Logout\Control
	{
		return $this->logoutControlFactory->create();
	}


	protected function createTemplate(): \Nette\Application\UI\ITemplate
	{
		$template = parent::createTemplate();

		$template->addFilter('dateTime', function (\DateTimeImmutable $s) {
			return $s->format('j. n. Y H:i:s');
		});

		return $template;
	}


	public function flashMessage($message, string $type = 'info'): \stdClass
	{
		if ($this->isAjax()) {
			$this->redrawControl('flashMessages');
		}

		return parent::flashMessage($message, $type);
	}

}
