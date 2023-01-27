# Changelog
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]
### Fixed
- Issue #3201543 by Aerzas, wengerk: Order items may not be product variations

### Changed
- move out from Travis CI to Github Actions
- modernize the Code Styles integration
- update changelog form to follow keepachangelog format

### Added
- add dependabot for Github Action dependency

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

[Unreleased]: https://github.com/antistatique/drupal-commerce-trustedshops/compare/8.x-2.0-rc1...HEAD
[2.0.0-rc1]: https://github.com/antistatique/drupal-commerce-trustedshops/compare/8.x-1.0-alpha2...8.x-2.0-rc1
[1.0.0-alpha2]: https://github.com/antistatique/drupal-commerce-trustedshops/compare/8.x-1.0-alpha1...8.x-1.0-alpha2
[1.0.0-alpha1]: https://github.com/antistatique/drupal-commerce-trustedshops/releases/tag/8.x-1.0-alpha1
