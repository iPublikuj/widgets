<?php
/**
 * Widget.php
 *
 * @copyright      More in license.md
 * @license        https://www.ipublikuj.eu
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 * @package        iPublikuj:Widgets!
 * @subpackage     Widgets
 * @since          1.0.0
 *
 * @date           15.09.14
 */

declare(strict_types = 1);

namespace IPub\Widgets\Widgets;

use Nette;
use Nette\Application;
use Nette\ComponentModel;
use Nette\Localization;
use Nette\Utils;

use IPub\Widgets;
use IPub\Widgets\Entities;
use IPub\Widgets\Exceptions;

/**
 * Widgets control definition
 *
 * @package        iPublikuj:Widgets!
 * @subpackage     Widgets
 *
 * @author         Adam Kadlec <adam.kadlec@ipublikuj.eu>
 *
 * @property Application\UI\ITemplate $template
 */
abstract class Widget extends Widgets\Application\UI\BaseControl implements IWidget
{
	/**
	 * @var Entities\IData
	 */
	protected $data;

	/**
	 * @param Entities\IData $data
	 * @param ComponentModel\IContainer|NULL $parent
	 * @param string|NULL $name
	 */
	public function __construct(
		Entities\IData $data,
		?ComponentModel\IContainer $parent = NULL,
		?string $name = NULL
	) {
		parent::__construct($parent, $name);

		$this->data = $data;
	}

	/**
	 * {@inheritdoc}
	 */
	public function getTitle() : string
	{
		$title = $this->data->getTitle();

		return $title ? $title : '';
	}

	/**
	 * {@inheritdoc}
	 */
	public function getDescription() : string
	{
		return $this->data->getDescription();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPriority() : int
	{
		return $this->data->getPriority();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getStatus() : bool
	{
		return $this->data->getStatus();
	}

	/**
	 * {@inheritdoc}
	 */
	public function getPosition() : string
	{
		return $this->data->getPosition();
	}

	/**
	 * Before render actions
	 */
	protected function beforeRender() : void
	{
		// Check if control has template
		if (!$this->template instanceof Nette\Bridges\ApplicationLatte\Template) {
			throw new Exceptions\InvalidStateException('Widgets container control is without template.');
		}

		// Get widget title
		$name = $this->getTitle();

		// If widget name has space...
		$pos = mb_strpos($name, ' ');
		if ($pos !== FALSE && mb_strpos($name, '||') === FALSE) {
			$title = Utils\Html::el('span')
				->addAttributes(['class' => 'color'])
				->setText(mb_substr($name, 0, $pos))
				->render();

			// Modify widget name
			$name = $title . mb_substr($name, $pos);
		}

		// If widget name has subtitle...
		$pos = mb_strpos($name, '||');
		if ($pos !== FALSE) {
			$title = Utils\Html::el('span')
				->addAttributes(['class' => 'title'])
				->setText(mb_substr($name, 0, $pos))
				->render();

			$subtitle = Utils\Html::el('span')
				->addAttributes(['class' => 'subtitle'])
				->setText(mb_substr($name, $pos + 2))
				->render();

			// Split name to title & subtitle
			$name = $title . $subtitle;
		}

		// Set badge if exists
		if ($badge = $this->data->getBadge()) {
			$badge = Utils\Html::el('i')
				->addAttributes(['class' => 'badge badge-' . $badge]);
		}

		// Set icon if exists
		if ($icon = $this->data->getIcon()) {
			$icon = Utils\Html::el('i')
				->addAttributes(['class' => 'ipub-icon ipub-icon-' . $icon]);
		}

		// Assign basic widget data to template
		$this->template->badge = $badge;
		$this->template->icon = $icon;
		$this->template->title = [
			'text'   => $name,
			'insert' => $this->data->getParam('widget.title.insert', TRUE),
			'hidden' => $this->data->getParam('widget.title.hidden', FALSE)
		];

		// Check if translator is available
		if ($this->getTranslator() instanceof Localization\ITranslator) {
			$this->template->setTranslator($this->getTranslator());
		}

		$templateFile = $this->getTemplate()->getFile();

		if (is_callable($templateFile)) {
			$templateFile = call_user_func($templateFile);
		}

		// If template was not defined before...
		if ($templateFile === NULL) {
			// Get component actual dir
			$dir = dirname($this->getReflection()->getFileName());

			// ...try to get base component template file
			$templateFile = $this->templateFile !== NULL && is_file($this->templateFile) ? $this->templateFile : $dir . DIRECTORY_SEPARATOR . 'template' . DIRECTORY_SEPARATOR . 'default.latte';
			$this->template->setFile($templateFile);
		}
	}

	/**
	 * Render widget
	 */
	public function render() : void
	{
		$this->beforeRender();

		// Render component template
		$this->template->render();
	}

	/**
	 * Convert widget name to string representation
	 *
	 * @return string
	 */
	public function __toString()
	{
		$class = explode('\\', get_class($this));

		return end($class);
	}
}
