
# ZYXEL MES3500-24 by SNMP

## Overview

https://service-provider.zyxel.com/emea/en/products/carrier-and-access-switches/access-switches/mes3500-series

### Known Issues

Description: Incorrect handling of SNMP bulk requests. Disable the use of bulk requests in the SNMP interface settings.
Version: all versions firmware
Device: ZYXEL MES3500-24

## Requirements

Zabbix version: 8.0 and higher.

## Tested versions

This template has been tested on:
- ZYXEL MES3500-24 V4.00(AABB.4)b1_20180502 | 05/02/2018

## Configuration

> Zabbix should be configured according to the instructions in the [Templates out of the box](https://www.zabbix.com/documentation/8.0/manual/config/templates_out_of_the_box) section.

## Setup

Refer to the vendor documentation.

### Macros used

|Name|Description|Default|
|----|-----------|-------|
|{$ZYXEL.LLD.FILTER.IF.CONTROL.MATCHES}|<p>Triggers will be created only for interfaces whose description contains the value of this macro</p>|`CHANGE_IF_NEEDED`|
|{$SNMP.TIMEOUT}|<p>The time interval for SNMP agent availability trigger expression.</p>|`5m`|
|{$CPU.UTIL.CRIT}||`90`|
|{$MEMORY.UTIL.MAX}||`90`|
|{$ZYXEL.LLD.FILTER.IF.NAME.MATCHES}|<p>Filter by discoverable interface names.</p>|`.*`|
|{$ZYXEL.LLD.FILTER.IF.NAME.NOT_MATCHES}|<p>Filter to exclude discovered interfaces by name.</p>|`CHANGE_IF_NEEDED`|
|{$ZYXEL.LLD.FILTER.IF.LINKUPTYPE.MATCHES}|<p>Filter of discoverable link types.</p><p>0 - Down link</p><p>1 - Cooper link</p><p>2 - Fiber link</p>|`1\|2`|
|{$ZYXEL.LLD.FILTER.IF.LINKUPTYPE.NOT_MATCHES}|<p>Filter to exclude discovered by link types.</p>|`CHANGE_IF_NEEDED`|
|{$ZYXEL.LLD.FILTER.SFP.STATUS.MATCHES}|<p>Filter of discoverable status.</p><p>0 - OK with DDM</p><p>1 - OK without DDM</p><p>2 - nonoperational</p>|`1\|2`|
|{$ZYXEL.LLD.FILTER.SFP.STATUS.NOT_MATCHES}|<p>Filter to exclude discovered by status.</p>|`CHANGE_IF_NEEDED`|
|{$ZYXEL.LLD.FILTER.SFPDDM.DESC.MATCHES}|<p>Filter by discoverable SFP modules name.</p>|`.*`|
|{$ZYXEL.LLD.FILTER.SFPDDM.DESC.NOT_MATCHES}|<p>Filter to exclude discovered SFP modules by name.</p>|`N/A`|
|{$ZYXEL.LLD.SFP.UPDATE}|<p>Receiving data from the SFP module is slow, we do not recommend setting the interval less than 10 minutes.</p>|`10m`|

### Items

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|SNMP agent availability||Zabbix internal|zabbix[host,snmp,available]<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `1h`</p></li></ul>|
|Hardware model name|<p>MIB: RFC1213-MIB</p><p>A textual description of the entity.  This value</p><p>should include the full name and version</p><p>identification of the system's hardware type,</p><p>software operating-system, and networking</p><p>software.  It is mandatory that this only contain</p><p>printable ASCII characters.</p>|SNMP agent|zyxel.3500_24.model<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `6h`</p></li></ul>|
|Contact|<p>MIB: RFC1213-MIB</p><p>The textual identification of the contact person</p><p>for this managed node, together with information</p><p>on how to contact this person.</p>|SNMP agent|zyxel.3500_24.contact<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `6h`</p></li></ul>|
|Host name|<p>MIB: RFC1213-MIB</p><p>An administratively-assigned name for this</p><p>managed node.  By convention, this is the node's</p><p>fully-qualified domain name.</p>|SNMP agent|zyxel.3500_24.name<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `6h`</p></li></ul>|
|Location|<p>MIB: RFC1213-MIB</p><p>The physical location of this node (e.g.,</p><p>`telephone closet, 3rd floor').</p>|SNMP agent|zyxel.3500_24.location<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `6h`</p></li></ul>|
|MAC address|<p>MIB: IF-MIB</p><p>The interface's address at the protocol layer</p><p>immediately `below' the network layer in the</p><p>protocol stack.  For interfaces which do not have</p><p>such an address (e.g., a serial line), this object</p><p>should contain an octet string of zero length.</p>|SNMP agent|zyxel.3500_24.mac<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `6h`</p></li></ul>|
|Uptime (network)|<p>MIB: RFC1213-MIB</p><p>The time (in hundredths of a second) since the</p><p>network management portion of the system was last</p><p>re-initialized.</p>|SNMP agent|zyxel.3500_24.net.uptime<p>**Preprocessing**</p><ul><li><p>Custom multiplier: `0.01`</p></li></ul>|
|Uptime (hardware)|<p>MIB: HOST-RESOURCES-MIB</p><p>The amount of time since this host was last initialized.</p><p>Note that this is different from sysUpTime in the SNMPv2-MIB</p><p>[RFC1907] because sysUpTime is the uptime of the</p><p>network management portion of the system.</p>|SNMP agent|zyxel.3500_24.hw.uptime<p>**Preprocessing**</p><ul><li><p>Check for not supported value: `any error`</p><p>⛔️Custom on fail: Set value to: `0`</p></li><li><p>Custom multiplier: `0.01`</p></li></ul>|
|ZyNOS F/W Version|<p>MIB: ZYXEL-MES3500-24-MIB</p>|SNMP agent|zyxel.3500_24.fwversion<p>**Preprocessing**</p><ul><li><p>JavaScript: `The text is too long. Please see the template.`</p></li><li><p>Discard unchanged with heartbeat: `1d`</p></li></ul>|
|Hardware serial number|<p>MIB: ZYXEL-MES3500-24-MIB</p><p>Serial number</p>|SNMP agent|zyxel.3500_24.serialnumber<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `6h`</p></li></ul>|
|CPU utilization|<p>MIB: ZYXEL-MES3500-24-MIB</p><p>Show device CPU load in %, it's the snapshot of CPU load when</p><p>getting the values.</p>|SNMP agent|zyxel.3500_24.cpuusage|

### Triggers

|Name|Description|Expression|Severity|Dependencies and additional info|
|----|-----------|----------|--------|--------------------------------|
|ZYXEL: No SNMP data collection|<p>SNMP is not available for polling. Please check device connectivity and SNMP settings.</p>|`max(/ZYXEL MES3500-24 by SNMP/zabbix[host,snmp,available],{$SNMP.TIMEOUT})=0`|Warning||
|ZYXEL: Template does not match hardware|<p>This template is for Zyxel MES3500-24, but connected to {ITEM.VALUE}</p>|`last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.model)<>"MES3500-24"`|Info|**Manual close**: Yes|
|ZYXEL: Host has been restarted|<p>Uptime is less than 10 minutes.</p>|`(last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.hw.uptime)>0 and last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.hw.uptime)<10m) or (last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.hw.uptime)=0 and last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.net.uptime)<10m)`|Info|**Manual close**: Yes|
|ZYXEL: Firmware has changed|<p>Firmware version has changed. Acknowledge to close the problem manually.</p>|`last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.fwversion,#1)<>last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.fwversion,#2) and length(last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.fwversion))>0`|Info|**Manual close**: Yes|
|ZYXEL: Device has been replaced|<p>Device serial number has changed. Acknowledge to close the problem manually.</p>|`last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.serialnumber,#1)<>last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.serialnumber,#2) and length(last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.serialnumber))>0`|Info|**Manual close**: Yes|
|ZYXEL: High CPU utilization|<p>The CPU utilization is too high. The system might be slow to respond.</p>|`min(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.cpuusage,5m)>{$CPU.UTIL.CRIT}`|Warning||

### LLD rule Temperature discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Temperature discovery|<p>An entry in tempTable.</p><p>Index of temperature unit. 1:MAC, 2:CPU, 3:PHY</p>|SNMP agent|zyxel.3500_24.temp.discovery<p>**Preprocessing**</p><ul><li><p>JavaScript: `The text is too long. Please see the template.`</p></li></ul>|

### Item prototypes for Temperature discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Temperature "{#ZYXEL.TEMP.ID}"|<p>MIB: ZYXEL-MES3500-24-MIB</p><p>The current temperature measured at this sensor</p>|SNMP agent|zyxel.3500_24.temp[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `1h`</p></li></ul>|

### Trigger prototypes for Temperature discovery

|Name|Description|Expression|Severity|Dependencies and additional info|
|----|-----------|----------|--------|--------------------------------|
|ZYXEL: Temperature {#ZYXEL.TEMP.ID} is in critical state|<p>Please check the temperature</p>|`last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.temp[{#SNMPINDEX}])>{#ZYXEL.TEMP.THRESH.HIGH}`|Average||

### LLD rule Voltage discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Voltage discovery|<p>An entry in voltageTable.</p>|SNMP agent|zyxel.3500_24.volt.discovery<p>**Preprocessing**</p><ul><li><p>JavaScript: `The text is too long. Please see the template.`</p></li></ul>|

### Item prototypes for Voltage discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Nominal "{#ZYXEL.VOLT.NOMINAL}"|<p>MIB: ZYXEL-MES3500-24-MIB</p><p>The current voltage reading.</p>|SNMP agent|zyxel.3500_24.volt[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Custom multiplier: `0.001`</p></li><li><p>Discard unchanged with heartbeat: `1h`</p></li></ul>|

### Trigger prototypes for Voltage discovery

|Name|Description|Expression|Severity|Dependencies and additional info|
|----|-----------|----------|--------|--------------------------------|
|ZYXEL: Voltage {#ZYXEL.VOLT.NOMINAL} is in critical state|<p>Please check the power supply</p>|`last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.volt[{#SNMPINDEX}])<{#ZYXEL.VOLT.THRESH.LOW}`|Average||

### LLD rule Interface discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Interface discovery||SNMP agent|zyxel.3500_24.net.if.discovery|

### Item prototypes for Interface discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Port {#SNMPINDEX}: Speed Duplex|<p>MIB:  ZYXEL-MES3500-24-MIB</p><p>Transmission mode</p>|SNMP agent|zyxel.3500_24.net.if.speed_duplex[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `6h`</p></li></ul>|
|Port {#SNMPINDEX}: Interface description|<p>MIB:  ZYXEL-MES3500-24-MIB</p><p>A textual string containing information about the interface</p>|SNMP agent|zyxel.3500_24.net.if.name[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `6h`</p></li></ul>|
|Port {#SNMPINDEX}: Link type|<p>MIB:  ZYXEL-MES3500-24-MIB</p><p>Physical connection type</p>|SNMP agent|zyxel.3500_24.net.if.link_type[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `6h`</p></li></ul>|
|Port {#SNMPINDEX}: Interface name|<p>MIB: IF-MIB</p><p>A textual string containing information about the interface</p>|SNMP agent|zyxel.3500_24.net.if.descr[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `6h`</p></li></ul>|
|Port {#SNMPINDEX}: Operational status|<p>MIB: IF-MIB</p><p>The current operational state of the interface.</p><p>The testing(3) state indicates that no operational</p><p>packets can be passed.</p>|SNMP agent|zyxel.3500_24.net.if.operstatus[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `6h`</p></li></ul>|
|Port {#SNMPINDEX}: Administrative status|<p>MIB: IF-MIB</p><p>The desired state of the interface.  The</p><p>testing(3) state indicates that no operational</p><p>packets can be passed.</p>|SNMP agent|zyxel.3500_24.net.if.adminstatus[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `6h`</p></li></ul>|
|Port {#SNMPINDEX}: Incoming traffic|<p>MIB: IF-MIB</p><p>The total number of octets received on the interface,</p><p>including framing characters.</p>|SNMP agent|zyxel.3500_24.net.if.in.traffic[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Custom multiplier: `8`</p></li><li>Change per second</li></ul>|
|Port {#SNMPINDEX}: Incoming unicast packages|<p>MIB: IF-MIB</p><p>The number of packets, delivered by this sub-layer to a</p><p>higher (sub-)layer, which were not addressed to a multicast</p><p>or broadcast address at this sub-layer</p>|SNMP agent|zyxel.3500_24.net.if.in.ucastpkts[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li>Change per second</li></ul>|
|Port {#SNMPINDEX}: Incoming multicast packages|<p>MIB: IF-MIB</p><p>The number of packets, delivered by this sub-layer to a</p><p>higher (sub-)layer, which were addressed to a multicast</p><p>address at this sub-layer.  For a MAC layer protocol, this</p><p>includes both Group and Functional addresses.</p>|SNMP agent|zyxel.3500_24.net.if.in.multicastpkts[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li>Change per second</li></ul>|
|Port {#SNMPINDEX}: Incoming broadcast packages|<p>MIB: IF-MIB</p><p>The number of packets, delivered by this sub-layer to a</p><p>higher (sub-)layer, which were addressed to a broadcast</p><p>address at this sub-layer.</p>|SNMP agent|zyxel.3500_24.net.if.in.broadcastpkts[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li>Change per second</li></ul>|
|Port {#SNMPINDEX}: Outgoing traffic|<p>MIB: IF-MIB</p><p>The total number of octets transmitted out of the</p><p>interface, including framing characters.  This object is a</p><p>64-bit version of ifOutOctets.</p>|SNMP agent|zyxel.3500_24.net.if.out.traffic[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Custom multiplier: `8`</p></li><li>Change per second</li></ul>|
|Port {#SNMPINDEX}: Outgoing unicast packages|<p>MIB: IF-MIB</p><p>The total number of packets that higher-level protocols</p><p>requested be transmitted, and which were not addressed to a</p><p>multicast or broadcast address at this sub-layer, including</p><p>those that were discarded or not sent.</p>|SNMP agent|zyxel.3500_24.net.if.out.ucastpkts[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li>Change per second</li></ul>|
|Port {#SNMPINDEX}: Outgoing multicast packages|<p>MIB: IF-MIB</p><p>The total number of packets that higher-level protocols</p><p>requested be transmitted, and which were addressed to a</p><p>multicast address at this sub-layer, including those that</p><p>were discarded or not sent.  For a MAC layer protocol, this</p><p>includes both Group and Functional addresses.</p>|SNMP agent|zyxel.3500_24.net.if.out.multicastpkts[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li>Change per second</li></ul>|
|Port {#SNMPINDEX}: Outgoing broadcast packages|<p>MIB: IF-MIB</p><p>The total number of packets that higher-level protocols</p><p>requested be transmitted, and which were addressed to a</p><p>broadcast address at this sub-layer, including those that</p><p>were discarded or not sent.</p>|SNMP agent|zyxel.3500_24.net.if.out.broadcastpkts[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li>Change per second</li></ul>|
|Port {#SNMPINDEX}: Link speed|<p>MIB: IF-MIB</p><p>An estimate of the interface's current bandwidth in bits per second</p>|SNMP agent|zyxel.3500_24.net.if.highspeed[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Custom multiplier: `1000000`</p></li><li><p>Discard unchanged with heartbeat: `6h`</p></li></ul>|
|Port {#SNMPINDEX}: Incoming utilization|<p>Interface utilization percentage</p>|Calculated|zyxel.3500_24.net.if.in.util[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>In range: `0 -> 100`</p><p>⛔️Custom on fail: Discard value</p></li><li><p>JavaScript: `The text is too long. Please see the template.`</p></li></ul>|
|Port {#SNMPINDEX}: Outgoing utilization|<p>Interface utilization percentage</p>|Calculated|zyxel.3500_24.net.if.out.util[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>In range: `0 -> 100`</p><p>⛔️Custom on fail: Discard value</p></li><li><p>JavaScript: `The text is too long. Please see the template.`</p></li></ul>|

### Trigger prototypes for Interface discovery

|Name|Description|Expression|Severity|Dependencies and additional info|
|----|-----------|----------|--------|--------------------------------|
|ZYXEL: Port {#SNMPINDEX}: Link down|<p>This trigger expression works as follows:<br>1. It can be triggered if the operations status is down.<br>2. `{$IFCONTROL:"{#IFNAME}"}=1` - a user can redefine context macro to value - 0. That marks this interface as not important. No new trigger will be fired if this interface is down.<br>3. `{TEMPLATE_NAME:METRIC.diff()}=1` - the trigger fires only if the operational status was up to (1) sometime before (so, do not fire for the 'eternal off' interfaces.)<br><br>WARNING: if closed manually - it will not fire again on the next poll, because of .diff.</p>|`last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.net.if.operstatus[{#SNMPINDEX}])=2 and last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.net.if.operstatus[{#SNMPINDEX}],#1)<>last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.net.if.operstatus[{#SNMPINDEX}],#2)`|Average|**Manual close**: Yes|

### LLD rule SFP without DDM discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|SFP without DDM discovery|<p>SFP module discovery.</p>|SNMP agent|zyxel.3500_24.sfp.discovery|

### Item prototypes for SFP without DDM discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|SFP {#SNMPINDEX}: Status|<p>MIB: ZYXEL-MES3500-24-MIB</p><p>Transceiver module status.</p>|SNMP agent|zyxel.3500_24.sfp.status[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `1h`</p></li></ul>|
|SFP {#SNMPINDEX}: Vendor|<p>MIB: ZYXEL-MES3500-24-MIB</p><p>Transceiver module vendor name.</p>|SNMP agent|zyxel.3500_24.sfp.vendor[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `1h`</p></li></ul>|
|SFP {#SNMPINDEX}: Part number|<p>MIB: ZYXEL-MES3500-24-MIB</p><p>Part number provided by transceiver module vendor.</p>|SNMP agent|zyxel.3500_24.sfp.part[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `1h`</p></li></ul>|
|SFP {#SNMPINDEX}: Serial number|<p>MIB: ZYXEL-MES3500-24-MIB</p><p>Serial number provided by transceiver module vendor.</p>|SNMP agent|zyxel.3500_24.sfp.serialnumber[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `1h`</p></li></ul>|
|SFP {#SNMPINDEX}: Revision|<p>MIB: ZYXEL-MES3500-24-MIB</p><p>Revision level for part number provided by transceiver module vendor.</p>|SNMP agent|zyxel.3500_24.sfp.revision[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `1h`</p></li></ul>|
|SFP {#SNMPINDEX}: Date code|<p>MIB: ZYXEL-MES3500-24-MIB</p><p>Transceiver module vendor's manufacturing date code.</p>|SNMP agent|zyxel.3500_24.sfp.datecode[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `1h`</p></li></ul>|
|SFP {#SNMPINDEX}: Transceiver|<p>MIB: ZYXEL-MES3500-24-MIB</p><p>Transceiver module type names.</p>|SNMP agent|zyxel.3500_24.sfp.transceiver[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `1h`</p></li></ul>|

### Trigger prototypes for SFP without DDM discovery

|Name|Description|Expression|Severity|Dependencies and additional info|
|----|-----------|----------|--------|--------------------------------|
|ZYXEL: SFP {#SNMPINDEX} has been replaced|<p>SFP {#SNMPINDEX} serial number has changed. Acknowledge to close the problem manually.</p>|`last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.sfp.serialnumber[{#SNMPINDEX}],#1)<>last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.sfp.serialnumber[{#SNMPINDEX}],#2) and length(last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.sfp.serialnumber[{#SNMPINDEX}]))>0`|Info|**Manual close**: Yes|

### LLD rule SFP with DDM discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|SFP with DDM discovery|<p>SFP DDM module discovery.</p>|SNMP agent|zyxel.3500_24.sfp.ddm.discovery<p>**Preprocessing**</p><ul><li><p>JavaScript: `The text is too long. Please see the template.`</p></li></ul>|

### Item prototypes for SFP with DDM discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|SFP {#ZYXEL.SFP.PORT}: {#ZYXEL.SFP.DESCRIPTION}|<p>MIB: ZYXEL-MES3500-24-MIB</p><p>Transceiver module DDM data ({#ZYXEL.SFP.DESCRIPTION}).</p>|SNMP agent|zyxel.3500_24.sfp.ddm[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Custom multiplier: `0.01`</p></li><li><p>Discard unchanged with heartbeat: `6h`</p></li></ul>|

### Trigger prototypes for SFP with DDM discovery

|Name|Description|Expression|Severity|Dependencies and additional info|
|----|-----------|----------|--------|--------------------------------|
|ZYXEL: SFP {#ZYXEL.SFP.PORT}: High {#ZYXEL.SFP.DESCRIPTION}|<p>The upper threshold value of the parameter is exceeded</p>|`last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.sfp.ddm[{#SNMPINDEX}]) > {#ZYXEL.SFP.WARN.MAX}`|Warning|**Manual close**: Yes|
|ZYXEL: SFP {#ZYXEL.SFP.PORT}: Low {#ZYXEL.SFP.DESCRIPTION}|<p>The parameter values are less than the lower threshold</p>|`last(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.sfp.ddm[{#SNMPINDEX}]) < {#ZYXEL.SFP.WARN.MIN}`|Warning|**Manual close**: Yes|

### LLD rule Memory pool discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Memory pool discovery||SNMP agent|zyxel.3500_24.memory.discovery|

### Item prototypes for Memory pool discovery

|Name|Description|Type|Key and additional info|
|----|-----------|----|-----------------------|
|Memory "{#ZYXEL.MEMORY.NAME}" utilization|<p>MIB: ZYXEL-MES3500-24-MIB</p><p>Utilization of memory pool in %.</p>|SNMP agent|zyxel.3500_24.memory[{#SNMPINDEX}]<p>**Preprocessing**</p><ul><li><p>Discard unchanged with heartbeat: `1h`</p></li></ul>|

### Trigger prototypes for Memory pool discovery

|Name|Description|Expression|Severity|Dependencies and additional info|
|----|-----------|----------|--------|--------------------------------|
|ZYXEL: High memory utilization in "{#ZYXEL.MEMORY.NAME}" pool|<p>The system is running out of free memory.</p>|`min(/ZYXEL MES3500-24 by SNMP/zyxel.3500_24.memory[{#SNMPINDEX}],5m)>{$MEMORY.UTIL.MAX}`|Average||

## Feedback

Please report any issues with the template at [`https://support.zabbix.com`](https://support.zabbix.com)

You can also provide feedback, discuss the template, or ask for help at [`ZABBIX forums`](https://www.zabbix.com/forum/zabbix-suggestions-and-feedback)

