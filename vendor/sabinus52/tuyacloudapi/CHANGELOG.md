# Changelog

### Version 2.0.1 - 17/01/2022

- Increase cache delay for discovery


### Version 2.0.0 - 19/02/2021

Quelques changements majeurs ([voir la release note](RELEASE-2.md)) :
- Aucun changement sur la session
- Création d'une classe abstraite `Request` pour chaque type de requêtes `DiscoveryRequest`, `QueryRequest` et `ControlRequest`
- Membre d'une `Session` inclus dans les classes `Device`
- Suppression de la classe `DeviceEvent`
- Ajout de fonction de test de connexion
- Corrections et améliorations diverses


### v1.2.0 - 21/01/2021

- Thermostat (x10)


### v1.1.0 - 07/01/2021

- Save token in the file system


### v1.0.1 - 19/11/2020

- Library `PhpColors` frozen version in 0.*


### v1.0 - 02/11/2020

- Fix return data null
- Add object **Climate**
- Librairie `GuzzleHttp` frozen version in 6.5


### v0.7 - 27/06/2020

- Add source example
- Timeout configurable
- Fix


### v0.6.1 - 24/01/2020

- Fix compatibility


### v0.6 - 11/12/2019

- Fix reserved word "switch"


### v0.5 - 04/11/2019

- Fix expiration time of token


### v0.4 - 25/10/2019

- Get list of type device available
- Fix server tuya default


### v0.3 - 29/09/2019

- Implementation of requests exceptions
- Implementation of unknown devices


### v0.2.1 - 01/07/2019

- Change version `PhpColor` in Composer
- Fix light device


### v0.2 - 29/06/2019

- Add class `Color` for to control light


### v0.1 - 08/05/2019

Initial release (version beta)