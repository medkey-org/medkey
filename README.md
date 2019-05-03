Medkey EHR/EMR Hospital Information System
==========================================

## General

Medkey is free and open source hospital/clinic information system (HIS application) with integrated Electronic Health 
Record (EHR/EMR) features. It is web application build on top of the latest version of PHP7.

Licensed under GPL 3.0 and can be used for free at any medical organization (government, private, etc.).

## Support & resources

* Website: https://medkey.org
* Wiki: https://medkey.org/wiki
* Demo: https://medkey.org/demo
* Public E-mail for anything: info@medkey.org
* Telegram news channel: https://t.me/medkey
* Telegram support group: https://t.me/medkey_talks

## Technological stack

Medkey is a modern web application, mainly based at backend (frontend is thin).

Current stack consists of:

* PHP7.3+;
* Yii2 Framework and Symfony4 at the infrastructure core of the system;
* PostgreSQL/MySQL complete support, hypothetical support of other popular DBMSes (MS SQL, Oracle, MongoDb);
* RabbitMQ at the core of integration, background and realtime functions;
* Webpack, ReactJS and Backbone on frontend.

## Modularity and extensibility

Medkey allow you to extend general branch of application by your modules and plugins. 

Modules contain additional features, which can be absolutely separate from existing features, or, if neede, can integrate
with basic modules.

Plugins extends existing functionality (can be extended basic modules, or other developers modules).

For modules development look for wiki.

## Quick start

All instructions available on wiki at https://medkey.org/wiki.
