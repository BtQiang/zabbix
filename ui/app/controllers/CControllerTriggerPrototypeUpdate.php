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


class CControllerTriggerPrototypeUpdate extends CController {

	protected function init(): void {
		$this->setInputValidationMethod(self::INPUT_VALIDATION_FORM);
		$this->setPostContentType(self::POST_CONTENT_TYPE_JSON);
	}

	public static function getValidationRules(): array {
		return ['object', 'fields' => [
			'triggerid' => ['db triggers.triggerid', 'required'],
			'name' => ['db triggers.description', 'required', 'not_empty'],
			'event_name' => ['db triggers.event_name'],
			'opdata' => ['db triggers.opdata'],
			'priority' => ['db triggers.priority', 'required', 'in' => [TRIGGER_SEVERITY_NOT_CLASSIFIED,
				TRIGGER_SEVERITY_INFORMATION, TRIGGER_SEVERITY_WARNING, TRIGGER_SEVERITY_AVERAGE, TRIGGER_SEVERITY_HIGH,
				TRIGGER_SEVERITY_DISASTER
			]],
			'expression' => ['string', 'required', 'not_empty',
				'use' => [CTriggerExpressionParser::class, ['usermacros' => true, 'lldmacros' => true]]
			],
			'recovery_mode' => ['db triggers.recovery_mode', 'in' => [ZBX_RECOVERY_MODE_EXPRESSION,
				ZBX_RECOVERY_MODE_RECOVERY_EXPRESSION, ZBX_RECOVERY_MODE_NONE
			]],
			'recovery_expression' => ['string', 'required', 'not_empty',
				'use' => [CTriggerExpressionParser::class, ['usermacros' => true, 'lldmacros' => true]],
				'when' => [
					['recovery_mode', 'in' => [ZBX_RECOVERY_MODE_RECOVERY_EXPRESSION]]
				]
			],
			'type' => ['db triggers.type', 'in' => [TRIGGER_MULT_EVENT_DISABLED, TRIGGER_MULT_EVENT_ENABLED]],
			'correlation_mode' => ['db triggers.correlation_mode', 'in' => [ZBX_TRIGGER_CORRELATION_NONE,
				ZBX_TRIGGER_CORRELATION_TAG
			]],
			'correlation_tag' => ['db triggers.correlation_tag', 'required', 'not_empty', 'when' => [
				['correlation_mode', 'in' => [ZBX_TRIGGER_CORRELATION_TAG]]
			]],
			'manual_close' => ['db triggers.manual_close', 'in' => [ZBX_TRIGGER_MANUAL_CLOSE_NOT_ALLOWED,
				ZBX_TRIGGER_MANUAL_CLOSE_ALLOWED
			]],
			'url_name' => ['db triggers.url_name'],
			'url' => ['db triggers.url', 'use' => [CUrlValidator::class, []]],
			'description' => ['db triggers.comments'],
			'status' => ['db triggers.status', 'in' => [TRIGGER_STATUS_ENABLED, TRIGGER_STATUS_DISABLED]],
			'tags' => ['objects', 'uniq' => ['tag', 'value'],
				'messages' => ['uniq' => _('Tag name and value combination is not unique.')],
				'fields' => [
					'value' => ['db trigger_tag.value'],
					'tag' => ['db trigger_tag.tag', 'required', 'not_empty', 'when' => ['value', 'not_empty']]
				]
			],
			'dependencies' => ['array', 'field' => ['db triggers.triggerid']],
			'hostid' => ['db hosts.hostid'],
			'discover' => ['db triggers.discover', 'in' => [ZBX_PROTOTYPE_DISCOVER, ZBX_PROTOTYPE_NO_DISCOVER]],
			'parent_discoveryid' => ['db triggers.triggerid'],
			'context' => ['string', 'in' => ['host', 'template']]
		]];
	}

	protected function checkInput(): bool {
		$ret = $this->validateInput(self::getValidationRules());

		if (!$ret) {
			$form_errors = $this->getValidationError();
			$response = $form_errors
				? ['form_errors' => $form_errors]
				: ['error' => [
					'title' => _('Cannot update trigger prototype'),
					'messages' => array_column(get_and_clear_messages(), 'message')
				]];

			$this->setResponse(
				new CControllerResponseData(['main_block' => json_encode($response)])
			);
		}

		return $ret;
	}

	protected function checkPermissions(): bool {
		return $this->getInput('context') === 'host'
			? $this->checkAccess(CRoleHelper::UI_CONFIGURATION_HOSTS)
			: $this->checkAccess(CRoleHelper::UI_CONFIGURATION_TEMPLATES);
	}

	protected function doAction(): void {
		$db_trigger_prototypes = API::TriggerPrototype()->get([
			'output' => ['expression', 'description', 'url_name', 'url', 'status', 'priority', 'comments', 'templateid',
				'type', 'recovery_mode', 'recovery_expression', 'correlation_mode', 'correlation_tag', 'manual_close',
				'opdata', 'discover', 'event_name'
			],
			'selectDependencies' => ['triggerid'],
			'selectTags' => ['tag', 'value'],
			'triggerids' => $this->getInput('triggerid'),
			'editable' => true
		]);

		if ($db_trigger_prototypes) {
			$db_trigger_prototypes = CMacrosResolverHelper::resolveTriggerExpressions($db_trigger_prototypes,
				['sources' => ['expression', 'recovery_expression']]
			);
			$db_trigger_prototype = reset($db_trigger_prototypes);
		}
		else {
			$db_trigger_prototype = null;
		}

		$trigger_prototype = [];

		if ($db_trigger_prototype && $db_trigger_prototype['templateid'] == 0) {
			$trigger_prototype += [
				'description' => $this->getInput('name'),
				'event_name' => $this->getInput('event_name', ''),
				'opdata' => $this->getInput('opdata', ''),
				'expression' => $this->getInput('expression'),
				'recovery_mode' => $this->getInput('recovery_mode', ZBX_RECOVERY_MODE_EXPRESSION),
				'manual_close' => $this->getInput('manual_close', ZBX_TRIGGER_MANUAL_CLOSE_NOT_ALLOWED)
			];

			switch ($trigger_prototype['recovery_mode']) {
				case ZBX_RECOVERY_MODE_RECOVERY_EXPRESSION:
					$trigger_prototype['recovery_expression'] = $this->getInput('recovery_expression', '');
				// break; is not missing here.

				case ZBX_RECOVERY_MODE_EXPRESSION:
					$trigger_prototype['correlation_mode'] = $this->getInput('correlation_mode', ZBX_TRIGGER_CORRELATION_NONE);

					if ($trigger_prototype['correlation_mode'] == ZBX_TRIGGER_CORRELATION_TAG) {
						$trigger_prototype['correlation_tag'] = $this->getInput('correlation_tag', '');
					}
					break;
			}
		}

		$tags = $this->getInput('tags', []);

		// Unset empty and inherited tags.
		foreach ($tags as $key => $tag) {
			if ($tag['tag'] === '' && $tag['value'] === '') {
				unset($tags[$key]);
			}
			elseif (array_key_exists('type', $tag) && !($tag['type'] & ZBX_PROPERTY_OWN)) {
				unset($tags[$key]);
			}
			else {
				unset($tags[$key]['type']);
			}
		}

		$trigger_prototype += [
			'type' => $this->getInput('type', 0),
			'dependencies' => zbx_toObject($this->getInput('dependencies', []), 'triggerid'),
			'triggerid' => $this->getInput('triggerid')
		];

		if ($db_trigger_prototype) {
			foreach (['url', 'url_name'] as $element) {
				$input_element = $this->getInput($element);

				if ($db_trigger_prototype[$element] !== $input_element) {
					$trigger_prototype[$element] = $input_element;
				}
			}

			$priority = $this->getInput('priority');

			if ($db_trigger_prototype['priority'] != $priority) {
				$trigger_prototype['priority'] = $priority;
			}

			$description = $this->getInput('description');

			if ($db_trigger_prototype['comments'] !== $description) {
				$trigger_prototype['comments'] = $description;
			}

			$status = $this->hasInput('status') ? TRIGGER_STATUS_ENABLED : TRIGGER_STATUS_DISABLED;

			if ($db_trigger_prototype['status'] != $status) {
				$trigger_prototype['status'] = $status;
			}

			$discover = $this->hasInput('discover') ? ZBX_PROTOTYPE_DISCOVER : ZBX_PROTOTYPE_NO_DISCOVER;

			if ($db_trigger_prototype['discover'] != $discover) {
				$trigger_prototype['discover'] = $discover;
			}

			CArrayHelper::sort($db_trigger_prototype['tags'], ['tag', 'value']);
			CArrayHelper::sort($tags, ['tag', 'value']);

			if (array_values($db_trigger_prototype['tags']) !== array_values($tags)) {
				$trigger_prototype['tags'] = $tags;
			}
		}

		$result = (bool) API::TriggerPrototype()->update($trigger_prototype);

		if ($result) {
			$output['success']['title'] = _('Trigger prototype updated');

			if ($messages = get_and_clear_messages()) {
				$output['success']['messages'] = array_column($messages, 'message');
			}
		}
		else {
			$output['error'] = [
				'title' => _('Cannot update trigger prototype'),
				'messages' => array_column(get_and_clear_messages(), 'message')
			];
		}

		$this->setResponse(new CControllerResponseData(['main_block' => json_encode($output)]));
	}
}
