CHANGELOG
---------

## NEXT RELEASE
 - Issue #3201543 by Aerzas, wengerk: Order items may not be product variations
 - move out from Travis CI to Github Actions 
 - modernize the Code Styles integration

## 8.x-2.0-rc1 (2020-07-05)
 - replace drupal_ti by wengerk/docker-drupal-for-contrib
 - Issue #3146835 by Project Update Bot: Automated Drupal 9 compatibility fixes
 - Issue #3090752 by wengerk, Tolyan4ik: Drupal 9 Readiness

## 8.x-1.x-alpha2 (2019-08-11)
 - Issue #3087963: Wrong language of Email sent by TrustedShops when using the Manual Trigger form

## 8.x-1.x-alpha1 (2019-08-10)
  - Add a new entity named Shop (commerce_trustedshops_shop) to deal with TrustedShops TIDs
  - Unit tested and covered CRUD operations on the Shop entity
  - Expose a themeable TrustedBadge block
  - `block--commerce-trustedshops-trustedbadge.html.twig`
  - Ensure customization of TrustedBadge block via template override (`block--commerce-trustedshops-trustedbadge.html.twig`)
  - Give access to a setting form for updating configuration
  - Allow user to enable or disable the Production/QA mode
  - Expose a Checkout pane "TrustedShop review collector after checkout" to collect review on checkout process
  - Add a Views Link field "TrustedShops: Link to invite writing a review of Order" to be added on Order's views as operations.
  - Expose an "Invite to Review" form allowing triggering Review invitations by TrustedShops

## 0.0.0 (2019-09-11)
  - Work in progress
