
# Jamf Plugin for GLPI

[![License](https://img.shields.io/github/license/pluginsGLPI/jamf.svg?&label=License)](https://github.com/pluginsGLPI/jamf/blob/master/LICENSE)
[![Follow twitter](https://img.shields.io/twitter/follow/Teclib.svg?style=social&label=Twitter&style=flat-square)](https://twitter.com/teclib)
[![Telegram Group](https://img.shields.io/badge/Telegram-Group-blue.svg)](https://t.me/glpien)
[![Project Status: Active](http://www.repostatus.org/badges/latest/active.svg)](http://www.repostatus.org/#active)
[![GitHub release](https://img.shields.io/github/release/pluginsGLPI/jamf.svg)](https://github.com/pluginsGLPI/jamf/releases)
[![GitHub build](https://travis-ci.org/pluginsGLPI/jamf.svg?)](https://travis-ci.org/pluginsGLPI/jamf/)

The **Jamf Plugin** provides a robust integration between **Jamf Pro** and **GLPI**. It synchronizes Apple devices (macOS, iOS, iPadOS, tvOS) and their detailed metadata directly into your GLPI asset management system.


## Inventory Capabilities (Jamf → GLPI)

The plugin automatically maps Jamf Pro objects to native GLPI assets:

| Jamf Item Type | GLPI Destination | GLPI Object Type |
| --- | --- | --- |
| **Computers** | Computer | `Computer` |
| **Mobile Devices** | Phone or Computer | `Phone` (iPhone/iPad) or `Computer` (AppleTV) |
| **Software** | Software | `Software` + `Version` + `Installation` |
| **Extension Attributes** | Plugin Tables | Searchable fields via GLPI engine |

### Synchronized Data Points

* **Hardware & Network:** Model identifiers, UDID, Serial, Wi-Fi/Bluetooth MAC, and storage partitions.
* **OS Details:** Operating system name and precise versioning.
* **Financials:** Purchase orders, warranty dates, and AppleCare IDs (mapped to `Infocom`).
* **Security & State:** Activation Lock status, Supervision mode, and Lost Mode details.
* **User Mapping:** Automatic link to GLPI users based on Jamf `location.username`.


### MDM Commands Integration

The plugin allows users to issue MDM commands directly from the asset form in GLPI if the user has a Jamf account linked. The UI for MDM commands will adapt based on the user's Jamf permissions.


### Accessing Settings

Go to: **Tools** → **Jamf** → **Configuration**.

### Server Settings

* **JSS Server:** Your Jamf Pro URL (e.g., `https://your-company.jamfcloud.com`).
* **Credentials:** Jamf account with at least **Read** permissions for `Computers` and / or `Mobile Devices`.

### Sync & Extension Attributes

You can toggle specific data imports (Financials, Software, Components) to match your needs.

> [!WARNING]
> If you wish to synchronize extension attributes, you must have read permissions for the `Computer Extension Attributes` and/or `Device Extension Attributes`.

## Download

Releases can be donwloaded on [GitHub](https://github.com/PluginsGLPI/jamf/releases).

## Documentation

We maintain a detailed [documentation](http://glpi-plugins.rtfd.io/en/latest/jamf/index.html).


## Professional Services

The GLPI Network services are available through our [Partner's Network](http://www.teclib-edition.com/en/partners/).
We provide special training, bug fixes with editor subscription, contributions for new features, and more.

## Contributing

* Open a ticket for each bug/feature so it can be discussed
* Follow [development guidelines](http://glpi-developer-documentation.readthedocs.io/en/latest/plugins/index.html)
* Work on a new branch on your own fork
* Open a PR that will be reviewed by a developer

## Copying

* **Code**: you can redistribute it and/or modify it under the terms of the GNU General Public License ([GPL-2.0](https://www.gnu.org/licenses/gpl-2.0.en.html)).
