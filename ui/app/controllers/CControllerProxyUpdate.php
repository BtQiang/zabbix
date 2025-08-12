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


class CControllerProxyUpdate extends CController {

	protected function init(): void {
		$this->setInputValidationMethod(self::INPUT_VALIDATION_FORM);
		$this->setPostContentType(self::POST_CONTENT_TYPE_JSON);
	}

	public static function getValidationRules(): array {
		$api_uniq = ['proxy.get', ['name' => '{name}'], 'proxyid'];

		return ['object', 'api_uniq' => $api_uniq, 'fields' => [
			'proxyid' => ['db proxy.proxyid', 'required'],
			'name' => ['db proxy.name', 'required', 'not_empty'],
			'proxy_groupid' => ['db proxy.proxy_groupid'],
			'operating_mode' => ['db proxy.operating_mode', 'required', 'in' => [PROXY_OPERATING_MODE_ACTIVE, PROXY_OPERATING_MODE_PASSIVE]],
			'address' => [
				['db proxy.address', 'use' => [CIPParser::class, [
					'usermacros' => false, 'lldmacros' => false, 'macros' => true, 'v6' => ZBX_HAVE_IPV6
				]],
					'messages' => ['use' => _('Invalid address.')]
				],
				['db proxy.address', 'required', 'not_empty', 'when' => ['operating_mode', 'in' => [PROXY_OPERATING_MODE_PASSIVE]]]
			],
			'port' => ['db proxy.port', 'required', 'not_empty',
				'use' => [CPortParser::class, ['usermacros' => true]], 'messages' => ['use' => _('Incorrect port.')],
				'when' => ['operating_mode', 'in' => [PROXY_OPERATING_MODE_PASSIVE]]
			],
			'local_address' => ['db proxy.local_address', 'required', 'not_empty',
				'use' => [CIPParser::class, ['usermacros' => false, 'lldmacros' => false, 'macros' => true,
					'v6' => ZBX_HAVE_IPV6]], 'messages' => ['use' => _('Invalid address.')],
				'when' => ['proxy_groupid', 'not_empty']
			],
			'local_port' =>	['db proxy.local_port', 'required', 'not_empty',
				'use' => [CPortParser::class, ['usermacros' => true]], 'messages' => ['use' => _('Incorrect port.')],
				'when' => ['proxy_groupid', 'not_empty']
			],
			'allowed_addresses' => ['db proxy.allowed_addresses',
				'use' => [CIPRangeParser::class, ['v6' => ZBX_HAVE_IPV6, 'dns' => true, 'usermacros' => true, 'macros' => true], 'messages' => ['use' => _('Invalid address.')]]
			],
			'description' => ['db proxy.description'],
			'tls_accept' => ['db proxy.tls_accept'],
			'tls_accept_certificate' => ['boolean'],
			'tls_accept_psk' =>	['boolean'],
			'tls_accept_none' => [
				['boolean'],
				[
					'integer', 'required', 'in 1', 'when' => [['tls_accept_certificate', 'in 0'], ['tls_accept_psk', 'in 0']],
					'messages' => ['in' => _s('Incorrect value for field "%1$s": %2$s.', _('Connections from proxy'), _('cannot be empty'))]
				]
			],
			'tls_connect' => ['db proxy.tls_connect', 'required',
				'in' => [HOST_ENCRYPTION_NONE, HOST_ENCRYPTION_PSK, HOST_ENCRYPTION_CERTIFICATE],
				'when' => ['operating_mode', 'in' => [PROXY_OPERATING_MODE_PASSIVE]]
			],
			'tls_psk_identity' => ['db proxy.tls_psk_identity', 'not_empty',
				'when' => ['tls_accept_psk', true]],
			'tls_psk' => [
				['db proxy.tls_psk',
					'regex' => '/^(.{2})+$/',
					'messages' => ['regex' => _('PSK must be an even number of characters.')]
				],
				['db proxy.tls_psk',
					'regex' => '/.{32,}/',
					'messages' => ['regex' => _('PSK must be at least 32 characters long.')]
				],
				['db proxy.tls_psk',
					'regex' => '/^[0-9a-f]*$/i',
					'messages' => ['regex' => _('PSK must contain only hexadecimal characters.')]
				],
				['db proxy.tls_psk', 'not_empty', 'when' => ['tls_accept_psk', true]]
			],
			'tls_issuer' => ['db proxy.tls_issuer', 'when' => ['tls_connect', 'in' => [HOST_ENCRYPTION_CERTIFICATE]]],
			'tls_subject' => ['db proxy.tls_subject', 'when' => ['tls_connect', 'in' => [HOST_ENCRYPTION_CERTIFICATE]]],
			'custom_timeouts' => ['db proxy.custom_timeouts', 'in' => [ZBX_PROXY_CUSTOM_TIMEOUTS_DISABLED, ZBX_PROXY_CUSTOM_TIMEOUTS_ENABLED]],
			'timeout_zabbix_agent' => ['db proxy.timeout_zabbix_agent', 'required', 'not_empty',
				'use' => [CTimeUnitValidator::class, ['max' => SEC_PER_DAY, 'usermacros' => true]],
				'when' => ['custom_timeouts', 'in' => [ZBX_PROXY_CUSTOM_TIMEOUTS_ENABLED]]
			],
			'timeout_simple_check' =>	['db proxy.timeout_simple_check', 'required', 'not_empty',
				'use' => [CTimeUnitValidator::class, ['max' => SEC_PER_DAY, 'usermacros' => true]],
				'when' => ['custom_timeouts', 'in' => [ZBX_PROXY_CUSTOM_TIMEOUTS_ENABLED]]
			],
			'timeout_snmp_agent' =>		['db proxy.timeout_snmp_agent', 'required', 'not_empty',
				'use' => [CTimeUnitValidator::class, ['max' => SEC_PER_DAY, 'usermacros' => true]],
				'when' => ['custom_timeouts', 'in' => [ZBX_PROXY_CUSTOM_TIMEOUTS_ENABLED]]
			],
			'timeout_external_check' =>	['db proxy.timeout_external_check', 'required', 'not_empty',
				'use' => [CTimeUnitValidator::class, ['max' => SEC_PER_DAY, 'usermacros' => true]],
				'when' => ['custom_timeouts', 'in' => [ZBX_PROXY_CUSTOM_TIMEOUTS_ENABLED]]
			],
			'timeout_db_monitor' =>		['db proxy.timeout_db_monitor', 'required', 'not_empty',
				'use' => [CTimeUnitValidator::class, ['max' => SEC_PER_DAY, 'usermacros' => true]],
				'when' => ['custom_timeouts', 'in' => [ZBX_PROXY_CUSTOM_TIMEOUTS_ENABLED]]
			],
			'timeout_http_agent' =>		['db proxy.timeout_http_agent', 'required', 'not_empty',
				'use' => [CTimeUnitValidator::class, ['max' => SEC_PER_DAY, 'usermacros' => true]],
				'when' => ['custom_timeouts', 'in' => [ZBX_PROXY_CUSTOM_TIMEOUTS_ENABLED]]
			],
			'timeout_ssh_agent' =>		['db proxy.timeout_ssh_agent', 'required', 'not_empty',
				'use' => [CTimeUnitValidator::class, ['max' => SEC_PER_DAY, 'usermacros' => true]],
				'when' => ['custom_timeouts', 'in' => [ZBX_PROXY_CUSTOM_TIMEOUTS_ENABLED]]
			],
			'timeout_telnet_agent' =>	['db proxy.timeout_telnet_agent', 'required', 'not_empty',
				'use' => [CTimeUnitValidator::class, ['max' => SEC_PER_DAY, 'usermacros' => true]],
				'when' => ['custom_timeouts', 'in' => [ZBX_PROXY_CUSTOM_TIMEOUTS_ENABLED]]
			],
			'timeout_script' =>			['db proxy.timeout_script', 'required', 'not_empty',
				'use' => [CTimeUnitValidator::class, ['max' => SEC_PER_DAY, 'usermacros' => true]],
				'when' => ['custom_timeouts', 'in' => [ZBX_PROXY_CUSTOM_TIMEOUTS_ENABLED]]
			],
			'timeout_browser' =>		['db proxy.timeout_browser', 'required', 'not_empty',
				'use' => [CTimeUnitValidator::class, ['max' => SEC_PER_DAY, 'usermacros' => true]],
				'when' => ['custom_timeouts', 'in' => [ZBX_PROXY_CUSTOM_TIMEOUTS_ENABLED]]
			]
		]];
	}

	protected function checkInput(): bool {
		$ret = $this->validateInput(self::getValidationRules());

		if (!$ret) {
			$form_errors = $this->getValidationError();
			$response = $form_errors
				? ['form_errors' => $form_errors]
				: ['error' => [
					'title' => _('Cannot update proxy'),
					'messages' => array_column(get_and_clear_messages(), 'message')
				]];

			$this->setResponse(
				new CControllerResponseData(['main_block' => json_encode($response)])
			);
		}

		return $ret;
	}

	protected function checkPermissions(): bool {
		if (!$this->checkAccess(CRoleHelper::UI_ADMINISTRATION_PROXIES)) {
			return false;
		}

		return (bool) API::Proxy()->get([
			'output' => [],
			'proxyids' => $this->getInput('proxyid'),
			'editable' => true
		]);
	}

	protected function doAction(): void {
		$proxy = [];

		$this->getInputs($proxy, ['proxyid', 'name', 'operating_mode', 'description', 'tls_connect',
			'tls_psk_identity', 'tls_psk', 'tls_issuer', 'tls_subject'
		]);

		$proxy['proxy_groupid'] = $this->getInput('proxy_groupid', 0);

		if ($proxy['proxy_groupid'] != 0) {
			$proxy['local_address'] = $this->getInput('local_address');
			$proxy['local_port'] = $this->getInput('local_port');
		}

		switch ($this->getInput('operating_mode')) {
			case PROXY_OPERATING_MODE_ACTIVE:
				$proxy['allowed_addresses'] = $this->getInput('allowed_addresses', '');

				$proxy['tls_accept'] = ($this->hasInput('tls_accept_none') ? HOST_ENCRYPTION_NONE : 0)
					| ($this->hasInput('tls_accept_psk') ? HOST_ENCRYPTION_PSK : 0)
					| ($this->hasInput('tls_accept_certificate') ? HOST_ENCRYPTION_CERTIFICATE : 0);

				break;

			case PROXY_OPERATING_MODE_PASSIVE:
				$proxy['address'] = $this->getInput('address', '');
				$proxy['port'] = $this->getInput('port', '');

				break;
		}

		$proxy['custom_timeouts'] = $this->getInput('custom_timeouts', ZBX_PROXY_CUSTOM_TIMEOUTS_DISABLED);

		if ($proxy['custom_timeouts'] == ZBX_PROXY_CUSTOM_TIMEOUTS_ENABLED) {
			$this->getInputs($proxy, ['timeout_zabbix_agent', 'timeout_simple_check', 'timeout_snmp_agent',
				'timeout_external_check', 'timeout_db_monitor', 'timeout_http_agent', 'timeout_ssh_agent',
				'timeout_telnet_agent', 'timeout_script', 'timeout_browser'
			]);
		}

		$result = API::Proxy()->update($proxy);

		$output = [];

		if ($result) {
			$output['success']['title'] = _('Proxy updated');

			if ($messages = get_and_clear_messages()) {
				$output['success']['messages'] = array_column($messages, 'message');
			}
		}
		else {
			$output['error'] = [
				'title' => _('Cannot update proxy'),
				'messages' => array_column(get_and_clear_messages(), 'message')
			];
		}

		$this->setResponse(new CControllerResponseData(['main_block' => json_encode($output)]));
	}
}
