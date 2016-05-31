<?php
/**
 * BaseControl.php
 *
 * @copyright      Vice v copyright.php
 * @license        http://www.ipublikuj.eu
 * @author         Adam Kadlec http://www.ipublikuj.eu
 * @package        iPublikuj:Widgets!
 * @subpackage     Application
 * @since          1.0.0
 *
 * @date           24.07.13
 */

namespace IPub\Widgets\Application\UI;

use Nette;
use Nette\Application;
use Nette\Localization;

use IPub;
use IPub\Widgets\Exceptions;

/**
 * Extensions base control definition
 *
 * @package        iPublikuj:Widgets!
 * @subpackage     Application
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 *
 * @property Application\UI\ITemplate $template
 */
abstract class BaseControl extends Application\UI\Control
{
	/**
	 * @var string
	 */
	protected $templateFile;

	/**
	 * @var Localization\ITranslator
	 */
	protected $translator;

	/**
	 * @param Localization\ITranslator $translator
	 */
	public function injectTranslator(Localization\ITranslator $translator = NULL)
	{
		$this->translator = $translator;
	}

	/**
	 * Change default control template path
	 *
	 * @param string $templateFile
	 *
	 * @throws Exceptions\FileNotFoundException
	 */
	public function setTemplateFile($templateFile)
	{
		// Check if template file exists...
		if (!is_file($templateFile)) {
			// Get component actual dir
			$dir = dirname($this->getReflection()->getFileName());

			// ...check if extension template is used
			if (is_file($dir . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . $templateFile)) {
				$templateFile = $dir . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . $templateFile;

			} else if (is_file($dir . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . $templateFile . '.latte')) {
				$templateFile = $dir . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . $templateFile . '.latte';

			} else {
				// ...if not throw exception
				throw new Exceptions\FileNotFoundException(sprintf('Template file "%s" was not found.', $templateFile));
			}
		}

		$this->templateFile = $templateFile;
	}

	/**
	 * @param Localization\ITranslator $translator
	 */
	public function setTranslator(Localization\ITranslator $translator)
	{
		$this->translator = $translator;
	}

	/**
	 * @return Localization\ITranslator|null
	 */
	public function getTranslator()
	{
		if ($this->translator instanceof Localization\ITranslator) {
			return $this->translator;
		}

		return NULL;
	}
}
