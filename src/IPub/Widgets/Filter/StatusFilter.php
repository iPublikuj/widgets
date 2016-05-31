<?php
/**
 * StatusFilter.php
 *
 * @copyright	More in license.md
 * @license		http://www.ipublikuj.eu
 * @author		Adam Kadlec http://www.ipublikuj.eu
 * @package		iPublikuj:Widgets!
 * @subpackage	Filter
 * @since		5.0
 *
 * @date		26.06.15
 */

namespace IPub\Widgets\Filter;

use Nette;
use Nette\Application;

/**
 * Widgets status filter
 *
 * @package        iPublikuj:Widgets!
 * @subpackage     Filter
 *
 * @author         Adam Kadlec <adam.kadlec@fastybird.com>
 */
class StatusFilter extends FilterIterator
{
	const CLASSNAME = __CLASS__;

	/**
	 * @var integer
	 */
	protected $status;

	/**
	 * {@inheritdoc}
	 */
	public function __construct(\Iterator $iterator, array $options = [])
	{
		parent::__construct($iterator, $options);

		$this->status = isset($options['status']) ? $options['status'] : NULL;
	}

	/**
	 * {@inheritdoc}
	 */
	public function accept()
	{
		if ($this->status === NULL) {
			return TRUE;
		}

		return $this->status == parent::current()->getStatus();
	}
}
