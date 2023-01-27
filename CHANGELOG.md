# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Added
- add official support of drupal 10.0

### Changed
- re-enable PHPUnit Symfony Deprecation notice

### Fixed
- fix PHPUnit deprecated prophecy integration
- fix deprecated uncalling accessCheck relying on entity queries to check access
- fix usage of deprecated Symfony\Component\EventDispatcher\Event in favor of Symfony\Contracts\EventDispatcher\Event
- fix deprecation of theme classy for Drupal 10 compatibilities
- fix tests self::modules property must be declared protected
- fix Symfony 4.4 event dispatcher parameter order change

## [2.0.0] - 2023-01-27
### Fixed
- Issue #3201543 by Aerzas, wengerk: Order items may not be product variations

### Changed
- move out from Travis CI to Github Actions
- modernize the Code Styles integration
- update changelog form to follow keepachangelog format
- use PHP 8.1 for linters
- bump testing using drupal/commerce 2.26 => 2.33

### Added
- add dependabot for Github Action dependency
- add upgrade-status check

### Removed
- remove trigger github actions on every pull-request, keep only push
- drop support of drupal 8.8 & 8.9
- drop support of drupal below 9.3.x

## [2.0.0-rc1] - 2020-07-05
### Changed
- replace drupal_ti by wengerk/docker-drupal-for-contrib

### Fixed
- Issue #3146835 by Project Update Bot: Automated Drupal 9 compatibility fixes
- Issue #3090752 by wengerk, Tolyan4ik: Drupal 9 Readiness

## [1.0.0-alpha2] - 2019-08-11
### Fixed
- Issue #3087963: Wrong language of Email sent by TrustedShops when using the Manual Trigger form

## [1.0.0-alpha1] - 2019-08-10
### Added
- Add a new entity named Shop (commerce_trustedshops_shop) to deal with TrustedShops TIDs
- Unit tested and covered CRUD operations on the Shop entity
- Expose a themeable TrustedBadge block `block--commerce-trustedshops-trustedbadge.html.twig`
- Ensure customization of TrustedBadge block via template override (`block--commerce-trustedshops-trustedbadge.html.twig`)
- Give access to a setting form for updating configuration
- Allow user to enable or disable the Production/QA mode
- Expose a Checkout pane "TrustedShop review collector after checkout" to collect review on checkout process
- Add a Views Link field "TrustedShops: Link to invite writing a review of Order" to be added on Order's views as operations.
- Expose an "Invite to Review" form allowing triggering Review invitations by TrustedShops

[Unreleased]: https://github.com/antistatique/drupal-commerce-trustedshops/compare/8.x-2.0...HEAD
[2.0.0]: https://github.com/antistatique/drupal-commerce-trustedshops/compare/8.x-2.0-rc1...8.x-2.0
[2.0.0-rc1]: https://github.com/antistatique/drupal-commerce-trustedshops/compare/8.x-1.0-alpha2...8.x-2.0-rc1
[1.0.0-alpha2]: https://github.com/antistatique/drupal-commerce-trustedshops/compare/8.x-1.0-alpha1...8.x-1.0-alpha2
[1.0.0-alpha1]: https://github.com/antistatique/drupal-commerce-trustedshops/releases/tag/8.x-1.0-alpha1
