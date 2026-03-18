# jamf Plugin For GLPI

[![License](https://img.shields.io/github/license/pluginsGLPI/jamf.svg?&label=License&style=for-the-badge)](https://github.com/pluginsGLPI/jamf/blob/main/LICENSE)
![Static Badge](https://img.shields.io/badge/Project_Status-Active-green?style=for-the-badge)
![GitHub Actions Status](https://img.shields.io/github/actions/workflow/status/pluginsGLPI/jamf/continuous-integration.yml?style=for-the-badge)
[![GitHub release](https://img.shields.io/github/release/pluginsGLPI/jamf.svg?&style=for-the-badge)](https://github.com/pluginsGLPI/jamf/releases)
![GitHub Downloads](https://img.shields.io/github/downloads/pluginsGLPI/jamf/total?style=for-the-badge)

<p align="center">
  <img width="126" height="126" src="https://raw.githubusercontent.com/pluginsGLPI/jamf/main/Logo.png">
</p>


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


## Documentation

Full plugin documentation is available [here](https://help.glpi-project.org/doc-plugins/plugins-glpi/jamf).


## Professional Services

GLPI professional services are offered through the [Partner Network](http://www.teclib-edition.com/en/partners/):

- Specialized training
- Bug fixes with an editor subscription
- Contributions for new features
- Personalized support and consulting

Experience a tailored service with exclusive advantages and opportunities.


## Contributing

We welcome contributions! Here's how you can help:

- Report bugs or request features via [Issues](https://github.com/pluginsGLPI/sccm/issues)
- Follow the [development guidelines](http://glpi-developer-documentation.readthedocs.io/en/latest/plugins/index.html)
- Use [GitFlow](http://git-flow.readthedocs.io/) for branching
- Work on a new branch in your fork
- Submit a Pull Request (PR) for review


## Social Media

[![Facebook GLPI](https://img.shields.io/badge/Facebook-GLPI-1877F2.svg?style=for-the-badge)](https://www.facebook.com/glpiproject/)
[![X (formerly Twitter)](https://img.shields.io/badge/Twitter-GLPI%20Project-26A2FA.svg?style=for-the-badge)](https://x.com/GLPI_PROJECT)
[![YouTube GLPI](https://img.shields.io/badge/YouTube-GLPI-FF0033.svg?style=for-the-badge)](https://www.youtube.com/channel/UCoIMi7aKeIvQRxi7ggd6VNA)
[![Instagram GLPI](https://img.shields.io/badge/Instagram-GLPI-E1306C.svg?style=for-the-badge)](https://www.instagram.com/glpi_project/)
[![LinkedIn GLPI](https://img.shields.io/badge/LinkedIn-GLPI-0A66C2.svg?style=for-the-badge)](https://www.linkedin.com/products/teclib-glpi/)
[![Telegram GLPI](https://img.shields.io/badge/Telegram-GLPI-blue.svg?style=for-the-badge)](https://t.me/glpien)
