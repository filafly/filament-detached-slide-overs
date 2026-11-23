# Changelog

## v1.0.0 - 2026-11-23

First public release.

- `detached()` on any action opens it in a slide-over that floats off the edge of the screen as a rounded card.
- The optional plugin detaches every slide-over in a panel, including the database notifications panel; `detached(false)` keeps one on the edge.
- `gap()` and `radius()` settings, also available as the `--fi-detached-slide-over-gap` and `--fi-detached-slide-over-radius` CSS properties.
- Works from either side, in right-to-left layouts, in light and dark mode and on phones.
- Supports Filament 4.11+ and 5.6+ (the versions with left and right slide-overs).
