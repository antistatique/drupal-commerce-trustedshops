CHANGELOG
---------

## NEXT RELEASE
 - Issue #3188848 by Aerzas: The configuration keys tested in the hook_requirements are wrong or obsolete

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
