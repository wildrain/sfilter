# CLAUDE.md

This file provides guidance to Claude Code (claude.ai/code) when working with code in this repository.

## Project Overview

SFilter is a WordPress plugin that serves as a helper for Saudi Filter. It provides scaffolding for common WordPress plugin features including custom post types, metaboxes, AJAX, REST API, Elementor widgets, and admin pages.

## Build Commands

```bash
# Install dependencies
composer install

# Update autoloader after adding new classes
composer dump-autoload
```

## Architecture

### Entry Point
- `sfilter.php` - Main plugin file, singleton pattern initialization, defines constants (SFILTER_VERSION, SFILTER_FILE, SFILTER_PATH, SFILTER_URL, SFILTER_ASSETS)

### Namespace & Autoloading
All classes use the `SFilter` namespace. PSR-4 autoloading maps `includes/` directory to this namespace.

### Core Components (initialized in `init_plugin()`)

| Component | File | Purpose |
|-----------|------|---------|
| `Assets` | includes/Assets.php | Frontend script/style registration |
| `Ajax` | includes/Ajax.php | WordPress AJAX handlers (wp_ajax_*) |
| `API` | includes/API.php | REST API endpoints (sfilter/v1/*) |
| `Load_Elementor` | includes/Elementor.php | Elementor widget integration |
| `Generator` | includes/Generator.php | Custom post types/taxonomies via extended-cpts |
| `Customizer` | includes/Customizer.php | WordPress Customizer settings |
| `Admin` | includes/Admin.php | Admin-only features (is_admin check) |
| `Frontend` | includes/Frontend.php | Frontend-only features (shortcodes) |

### Admin Subsystem (includes/Admin/)
- `Menu.php` - Admin menu pages registration
- `Handler.php` - Admin form handlers
- `Settings.php` - Settings page logic
- `TestBgJob.php` - Background processing examples
- `views/` - Admin page templates

### Frontend Subsystem (includes/Frontend/)
- `Shortcode.php` - Shortcode definitions ([sf_shortcode], [sf_enquiry])
- `views/` - Shortcode templates

### Key Dependencies (composer)
- **extended-cpts** - Simplified post type/taxonomy registration. Use `register_extended_post_type()` and `register_extended_taxonomy()`
- **wp-background-processing** - Background job processing
- **Carbon** - DateTime handling

### Database
The plugin creates a custom table `{prefix}sfilter_data` on activation via `Installer.php`.

### Assets
- Frontend CSS/JS: `assets/css/`, `assets/js/`
- Scripts are registered (not enqueued) in Assets.php, then enqueued when needed (e.g., in shortcodes)

## Conventions

- Text domain: `sfilter`
- Option prefix: `sfilter_`
- AJAX actions: `sf_*`
- REST route base: `sfilter/v1`
- Elementor widget category: `sfilter-widgets`
