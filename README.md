# ChannelEngine Magento 2 plugin

Please read this document thoroughly in order to prepare for the correct installation.

## Installation instructions

### Manual installation
To manually install the extension, you will need to have direct access to the server through the terminal.

**Step 1:**
Extract the content of the `channelengine-ce-manual.zip` (for Magento Community Edition)
or `channelengine-ee-manual.zip` (for Magento Enterprise Edition) file and
upload `ChannelEngine` folder to your Magento shop `app/code/` directory
(copy the whole ChannelEngine folder). After you do that, 
make sure that you have folders `app/code/ChannelEngine/ChannelEngineIntegration`.

**Step 2:** Now module has to be enabled and Magento recompiled in order to include the module.

Login to Magento server via terminal (SSH) and go to the root directory of the Magento installation.
All the commands should be executed as the Magento _server console_ user.
This is the same console user that is used to install the Magento.

**Note:** If you previously uninstalled the module, then you need to enable the module first:
```bash
php bin/magento module:enable ChannelEngine_ChannelEngineIntegration

```

Run the following commands:
```bash
php bin/magento setup:upgrade
php bin/magento setup:di:compile
```

**Step 3:** Optionally you might need to fix permissions on your Magento files if
the previous steps were run as a `root` or other non-magento console user.

After installation is over, ChannelEngine configuration can be accessed with _Stores > ChannelEngine > Configuration_ menu.

## Uninstall instructions
### Marketplace installation
If module is installed through the Magento Marketplace, module can be uninstalled
from the _System > Web Setup Wizard > Extension manager_.

### Uninstall manually
In a case where module is installed manually, some manual actions are also required to remove the module.

Login to Magento server via terminal (SSH) and go to the root directory of the Magento installation.
All the commands should be executed as the Magento _server console_ user.
This is the same console user that is used to install the Magento.

**Step 1:** Disable module by running this command from the Magento root folder and as a magento console user:
```bash
php bin/magento module:disable ChannelEngine_ChannelEngineIntegration
```

**Step 2:** Remove module files
```bash
rm -rf app/code/ChannelEngine
```

**Step 3:** Delete module data from database (you will need the access to the database):
```sql
DELETE FROM `setup_module` WHERE `module` = 'ChannelEngine_ChannelEngineIntegration';
DROP TABLE `channel_engine_entity`;
DROP TABLE `channel_engine_events`;
DROP TABLE `channel_engine_logs`;
DROP TABLE `channel_engine_order`;
DROP TABLE `channel_engine_queue`;
```

For Enterprise Edition, delete returns table:
```sql
DROP TABLE `channel_engine_returns`;
```

**Step 4:** Lastly, rebuild Magento's code:
```bash
php bin/magento setup:upgrade
php bin/magento setup:di:compile
```

## Compatibility
Magento 2.3.x and 2.4.x versions

## Prerequisites
- PHP 7.1 or newer
- MySQL 5.6 or newer
