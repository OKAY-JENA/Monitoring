<?php declare(strict_types = 1);

namespace Pd\Monitoring\DashBoard\Controls\Settings;

trait TFactory
{

	private IFactory $settingsControlFactory;


	public function injectSettingsControlFactory(IFactory $factory): void
	{
		$this->settingsControlFactory = $factory;
	}


	protected function createComponentSettings(): Control
	{
		return $this->settingsControlFactory->create();
	}

}
