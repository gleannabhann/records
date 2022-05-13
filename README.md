# SCA Hall of Records
Order of Precedence and Martial Authorization Management System

This repository will not entertain pull requests from outside contributors until the `php74` branch is merged.

## Warnings & Labeling

_This codebase is still in pre-release, not confirmed stable._

Until the `php74` branch is merged into `main`, you should consider `main` to be The Bad Place.

## Feature List

- Order of Precedence for SCA Heralds
  - Tracks award dates, events, who gave award, etc.
- Authorizations for SCA Marshallate
  - Tracks current martial authorizations and marshal warrants
- Member info pages
  - Displays all known information about a member: awards, heraldry, authorizations - all in one place
  - Minimizes PII capture to only what's needed and only admins who need access to that information can view it
- Award info pages
  - Displays all known recipients of an award, in order from least recent to
    most recent
- Court report pages
  - List all awards presented during a specific event
- Campground Listing for Autocrats
  - Presents a browse-able list of commonly-used event sites with information
about rental fees, amenities, and points of contact
- API endpoints
  - Allow local branches to display members' Awards on local websites
  - Allow local branches to display information about branch Awards & recipients on local websites
  - Currently all API endpoints are read-only & there are no plans to change that
- Portable
  - Easy for other regional branches to set up and use, with no Gleann Abhann
    specific language used in the codebase

## Worklist
_Note: this repository is a sanitized fork of a private repo. 
Issues relating to the v2.0 milestone will be re-created in this repository soon. 
In the mean time, here's a brief list of some of what's planned for v2.0_

- Unit/integration testing and CI
- Semi-regular v2.x releases as features are implemented
- SemVer tagging with release notes & info about PHP/MySQL versions tested
- `stable` branch for stable code, allowing main to serve as the bleeding edge
- community-requested features
- Upgrade from Bootstrap 3 to Bootstrap 5
- PHP 8 Compatibility
- Overall code cleanup & standardization
- Logic that enforces organizational policy
- Build more auditing reports to help admins find incorrect data
- Expiration of PII for Person records with no active Auths/Warrants in the last _n_ days
- API person info endpoint updated to optionally include armory image blobs and allow requesting multiple person records in one call
- API key issuance structure to allow restricting API access to recognized branches
