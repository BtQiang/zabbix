<?php declare(strict_types = 0);
/*
** Copyright (C) 2001-2025 Zabbix SIA
**
** This program is free software: you can redistribute it and/or modify it under the terms of
** the GNU Affero General Public License as published by the Free Software Foundation, version 3.
**
** This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
** without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
** See the GNU Affero General Public License for more details.
**
** You should have received a copy of the GNU Affero General Public License along with this program.
** If not, see <https://www.gnu.org/licenses/>.
**/


class CButtonDropdown extends CButton {

	/**
	 * Default CSS class name for HTML root element.
	 */
	public const ZBX_STYLE_CLASS = 'btn-dropdown-container';

	/**
	 * Button style names.
	 */
	public const ZBX_STYLE_BTN_VALUE = 'dropdown-value';

	/**
	 * Selected value.
	 *
	 * @var string
	 */
	protected $dropdown_value = null;

	/**
	 * Dropdown items array.
	 *
	 * @var array
	 */
	protected $dropdown_items = [];

	/**
	 * Create CButtonDropdown instance.
	 *
	 * @param string $name              Element name.
	 * @param string $value             Element selected value.
	 * @param array  $items             Dropdown items.
	 * @param string $items[]['label']  Dropdown item label.
	 * @param string $items[]['value']  Dropdown item value.
	 * @param string $items[]['class']  Dropdown css class to be used for CButtonDropdown when item is selected.
	 * @param string caption            Button caption.
	 */

	public function __construct(string $name, $value = null, array $items = [], string $caption = '') {
		parent::__construct($name, $caption);

		$this->setId($this->getId().'_button');

		$this->dropdown_value = $value;
		$this->dropdown_items = $items;

		$this->addClass(ZBX_STYLE_BTN_ALT);
		$this->addClass(ZBX_STYLE_BTN_TOGGLE);
	}

	public function toString($destroy = true) {
		$this->setMenuPopup([
			'type' => 'dropdown',
			'data' => [
				'items' => $this->dropdown_items,
				'toggle_class' => ZBX_STYLE_BTN_TOGGLE
			]
		]);

		return (new CDiv())
			->addClass(self::ZBX_STYLE_CLASS)
			->setId($this->getId().'_wrap')
			->addItem(new CObject(parent::toString($destroy)))
			->addItem(
				(new CInput('hidden', $this->getAttribute('name'), $this->dropdown_value))
					->setAttribute('data-field-type', 'hidden')
					->addClass(self::ZBX_STYLE_BTN_VALUE)
					->setAttribute('data-skip-from-submit', $this->getAttribute('data-skip-from-submit'))
			)
			->toString(true);
	}
}
