# Developer Portfolio — WordPress FSE Theme

A modern, dark-first developer portfolio **Full Site Editing (FSE)** theme for WordPress. Built entirely with the block editor, it ships with 22 custom dynamic blocks, two custom post types, JSON-LD structured data, Open Graph / Twitter Card meta, security headers, accessibility features, and a fully automated setup on activation.

**Live demo:** [jeremyanderson.tech](https://jeremyanderson.tech)

---

## Table of Contents

1. [Requirements](#requirements)
2. [Installation](#installation)
3. [What Happens on Activation](#what-happens-on-activation)
4. [Theme Structure](#theme-structure)
5. [Custom Blocks](#custom-blocks)
6. [Custom Post Types & Taxonomies](#custom-post-types--taxonomies)
7. [Custom Meta Fields](#custom-meta-fields)
8. [Templates & Template Parts](#templates--template-parts)
9. [Block Patterns](#block-patterns)
10. [Design System (theme.json)](#design-system-themejson)
11. [Typography](#typography)
12. [JavaScript Features](#javascript-features)
13. [SEO & Structured Data](#seo--structured-data)
14. [Social & Open Graph Meta](#social--open-graph-meta)
15. [Security](#security)
16. [Accessibility](#accessibility)
17. [Developer Filters & Hooks](#developer-filters--hooks)
18. [FAQ Shortcode](#faq-shortcode)
19. [Migrations](#migrations)
20. [Customization Guide](#customization-guide)
21. [License](#license)

---

## Requirements

| Dependency | Minimum Version |
|------------|----------------|
| WordPress  | 6.4+           |
| PHP        | 8.0+           |

Tested up to WordPress 6.7.

---

## Installation

1. Copy (or clone) the `developer-portfolio` folder into `wp-content/themes/`.
2. In WordPress admin, go to **Appearance → Themes**.
3. Click **Activate** on **Developer Portfolio**.
4. The theme runs its activation routine automatically — see [What Happens on Activation](#what-happens-on-activation).

---

## What Happens on Activation

When you activate the theme, the following is created automatically:

- **Pages** — Home, Projects, Services, Blog, About, Accessibility, Privacy Policy, Terms of Service
- **Reading settings** — Static front page → Home; Posts page → Blog
- **Navigation** — A `wp_navigation` post called "Primary Navigation" with links to all main pages
- **Rewrite rules** — Flushed so custom post type URLs work immediately
- **Default "Sample Page"** — Deleted automatically

Each page is pre-populated with the appropriate custom blocks (hero, featured projects, tech stack, etc.) so the site looks complete out of the box.

---

## Theme Structure

```
developer-portfolio/
├── assets/
│   ├── css/
│   │   └── custom.css                  # All custom styles (32 KB) — animations, cards, badges, scrollbar, etc.
│   ├── fonts/                          # 10 self-hosted variable WOFF2 font files
│   │   ├── inter-var-latin.woff2
│   │   ├── inter-var-latin-italic.woff2
│   │   ├── jetbrains-mono-var-latin.woff2
│   │   ├── dm-sans-var-latin.woff2
│   │   ├── dm-sans-var-latin-italic.woff2
│   │   ├── playfair-display-var-latin.woff2
│   │   ├── source-serif-4-var-latin.woff2
│   │   ├── source-serif-4-var-latin-italic.woff2
│   │   ├── space-grotesk-var-latin.woff2
│   │   └── fira-code-var-latin.woff2
│   ├── images/
│   │   ├── favicon.svg                 # SVG favicon
│   │   └── logo-mark.svg              # SVG logo mark
│   └── js/
│       ├── theme.js                    # Frontend: scroll effects, animations, role rotation, mobile nav
│       ├── project-meta-sidebar.js     # Editor: Project meta fields sidebar panel
│       ├── post-meta-sidebar.js        # Editor: Blog post meta fields sidebar panel
│       ├── service-meta-sidebar.js     # Editor: Service meta fields sidebar (6 panels, 16 fields)
│       └── tech-stack-sidebar.js       # Editor: Tech stack management sidebar panel
├── blocks/                             # 22 custom dynamic blocks (each with block.json, edit.js, render.php)
│   ├── about-experience/
│   ├── about-intro/
│   ├── blog-listing/
│   ├── contact-cta/
│   ├── featured-projects/
│   ├── hero-section/
│   ├── latest-posts/
│   ├── projects-listing/
│   ├── services-grid/
│   ├── services-listing/
│   ├── services-hero/
│   ├── services-market-rates/
│   ├── services-pricing-table/
│   ├── services-packages/
│   ├── services-retainers/
│   ├── services-addons/
│   ├── services-comparison/
│   ├── services-faq/
│   ├── services-cta/
│   ├── site-header/
│   ├── social-links/
│   └── tech-stack/
├── parts/
│   ├── header.html                     # Template part — renders site-header custom block
│   └── footer.html                     # Template part — social links, legal pages, © {year}
├── patterns/                           # 9 PHP-based block patterns (legacy, kept for backwards compat)
│   ├── hero-section.php
│   ├── featured-projects.php
│   ├── tech-stack.php
│   ├── latest-posts.php
│   ├── contact-cta.php
│   ├── about-bio.php
│   ├── about-stats.php
│   ├── about-experience.php
│   └── about-education.php
├── templates/                          # 15 FSE templates
│   ├── front-page.html
│   ├── home.html
│   ├── index.html
│   ├── page.html
│   ├── single.html
│   ├── archive.html
│   ├── 404.html
│   ├── page-projects.html
│   ├── page-services.html
│   ├── page-blog.html
│   ├── page-about.html
│   ├── single-project.html
│   ├── single-service.html
│   ├── archive-project.html
│   └── archive-service.html
├── functions.php                       # Theme setup, CPTs, meta, blocks, SEO, migrations (~2800 lines)
├── style.css                           # Theme header metadata
├── theme.json                          # Full design system — colors, typography, spacing, shadows, layout
├── screenshot.png                      # Theme screenshot for WP admin
└── README.md
```

---

## Custom Blocks

All 22 blocks are **dynamic server-rendered** blocks (each has `block.json`, `edit.js`, and `render.php`). Every block's content is editable directly in the block editor via InspectorControls or inline RichText fields.

| Block | Slug | Description |
|-------|------|-------------|
| **Site Header** | `developer-portfolio/site-header` | Glassmorphism sticky header with editable nav items, logo, favicon, and CTA button |
| **Hero Section** | `developer-portfolio/hero-section` | Full-screen hero with animated gradient orbs, grid overlay, rotating role titles, badge, and two CTA buttons |
| **Featured Projects** | `developer-portfolio/featured-projects` | Displays selected projects from the Projects CPT in a responsive card grid |
| **Projects Listing** | `developer-portfolio/projects-listing` | Full projects listing page with all published projects |
| **Services Grid** | `developer-portfolio/services-grid` | Displays selected services from the Services CPT |
| **Services Listing** | `developer-portfolio/services-listing` | Full services listing page |
| **Services Hero** | `developer-portfolio/services-hero` | Full-width hero with gradient heading, eyebrow text, subtitle, and keyword chips |
| **Services Market Rates** | `developer-portfolio/services-market-rates` | 4-column market context bar showing competitor rate ranges |
| **Services Pricing Table** | `developer-portfolio/services-pricing-table` | Hourly rates table querying the Services CPT by category with volume discount callout |
| **Services Packages** | `developer-portfolio/services-packages` | Reusable 3-column tier cards — used for WordPress, AI, and Security package categories |
| **Services Retainers** | `developer-portfolio/services-retainers` | Monthly retainer cards with color bars, features, and terms callout |
| **Services Add-Ons** | `developer-portfolio/services-addons` | 3-column add-on grid with emoji icons, descriptions, and pricing |
| **Services Comparison** | `developer-portfolio/services-comparison` | Full comparison table (Jeremy vs Agency vs Offshore vs Freelancer) with color-coded cells |
| **Services FAQ** | `developer-portfolio/services-faq` | Collapsible FAQ accordion with FAQPage schema.org structured data |
| **Services CTA** | `developer-portfolio/services-cta` | Final call-to-action box with heading, description, and primary/secondary buttons |
| **Tech Stack** | `developer-portfolio/tech-stack` | Color-coded technology badges grouped by category, managed via page meta with an editor sidebar UI |
| **Latest Posts** | `developer-portfolio/latest-posts` | Recent blog posts section for the home page |
| **Blog Listing** | `developer-portfolio/blog-listing` | Full blog listing with all published posts |
| **About Intro** | `developer-portfolio/about-intro` | Bio section with image and stats counters |
| **About Experience** | `developer-portfolio/about-experience` | Work experience timeline and education/achievement cards |
| **Contact CTA** | `developer-portfolio/contact-cta` | Call-to-action card with gradient border and email link |
| **Social Links** | `developer-portfolio/social-links` | SVG icon links for GitHub, LinkedIn, X (Twitter), and email |

---

## Custom Post Types & Taxonomies

### Projects (`project`)

- **Icon:** `dashicons-portfolio`
- **Supports:** title, editor, thumbnail, excerpt, custom-fields
- **Archive:** `/project/`
- **Taxonomies:**
  - `project_category` — hierarchical (like categories)
  - `project_tag` — non-hierarchical (like tags)
- **Editor template** — pre-configured with bound meta field placeholders for subtitle, GitHub URL, and live URL

### Services (`service`)

- **Icon:** `dashicons-admin-tools`
- **Supports:** title, editor, thumbnail, excerpt, custom-fields
- **Archive:** `/service/`
- **Taxonomies:**
  - `service_category` — hierarchical (with admin column in list table)
- **Pre-populated categories:** Hourly Rates, WordPress Packages, AI Packages, Security Packages, Monthly Retainers, Add-Ons
- **Pre-populated posts:** 27 service entries across all categories with full pricing data
- **Editor template** — pre-configured with bound meta field blocks for subtitle, tier, price, price note, description, and CTA button. All bound fields are editable both inline in the content area and via the sidebar panels — changes sync automatically between both editors.

---

## Custom Meta Fields

All meta fields are registered with `show_in_rest: true` for block editor compatibility and include sanitization and auth callbacks.

### Project Meta (`project` post type)

| Key | Type | Description |
|-----|------|-------------|
| `project_subtitle` | `string` | Short subtitle displayed on project cards |
| `project_github_url` | `string` | GitHub repository URL (auto-sanitized as URL) |
| `project_live_url` | `string` | Live demo/site URL (auto-sanitized as URL) |

### Service Meta (`service` post type)

| Key | Type | Description |
|-----|------|-------------|
| `service_subtitle` | `string` | Short subtitle displayed on service cards |
| `service_price_range` | `string` | Price range text — legacy field used by services-grid block |
| `service_cta_url` | `string` | Call-to-action URL (auto-sanitized as URL) |
| `service_price` | `string` | Display price (e.g. "$115/hr", "$2,500", "$500/mo") |
| `service_price_note` | `string` | Price subtitle (e.g. "per hour", "flat fee · 3–4 weeks") |
| `service_tier` | `string` | Tier label (e.g. "STARTER", "PROFESSIONAL", "ENTERPRISE") |
| `service_features` | `string` | JSON-encoded array of feature strings for package cards |
| `service_icon` | `string` | Emoji icon for add-on cards |
| `service_badge_type` | `string` | Category badge color key: `wp`, `ai`, `sec`, `maint` |
| `service_featured` | `string` | "1" if marked as "Most Popular" (highlighted card) |
| `service_cta_text` | `string` | CTA button label (e.g. "Get a Quote →") |
| `service_typical_uses` | `string` | Comma-separated use cases for the pricing table |
| `service_min_hours` | `string` | Minimum engagement (e.g. "2 hrs") |
| `service_hours_included` | `string` | Hours included in retainer (e.g. "5 hrs/mo included") |
| `service_color_bar` | `string` | Hex color for retainer card top bar |
| `service_sort_order` | `string` | Numeric sort order within the category |

### Blog Post Meta (`post` post type)

| Key | Type | Description |
|-----|------|-------------|
| `post_subtitle` | `string` | Optional subtitle for blog posts |

### Page Meta (`page` post type)

| Key | Type | Description |
|-----|------|-------------|
| `tech_stack_data` | `string` | JSON-encoded tech stack categories and badges (managed via sidebar UI) |

### Editor Sidebar Panels

Custom JavaScript sidebar panels are enqueued in the block editor for managing meta fields:

- **`project-meta-sidebar.js`** — Adds a "Project Details" panel to the Project editor with fields for subtitle, GitHub URL, and live URL
- **`post-meta-sidebar.js`** — Adds a "Post Details" panel to the Post editor with a subtitle field
- **`service-meta-sidebar.js`** — Adds 6 collapsible panels to the Service editor covering all 16 meta fields: Pricing, Service Details, Features List, Hourly Rate Details, Retainer Details, and CTA Button. Fields sync live with inline bound blocks in the content area.
- **`tech-stack-sidebar.js`** — Adds a visual tech stack manager to the Page editor for adding/removing categories and badges

---

## Templates & Template Parts

### Template Parts

| Part | Area | Description |
|------|------|-------------|
| `header.html` | Header | Renders the `developer-portfolio/site-header` custom block |
| `footer.html` | Footer | Social links, legal page links (Terms, Privacy, Accessibility), dynamic `© {year}` |

### Templates (15 total)

| Template | Description |
|----------|-------------|
| `front-page.html` | Static front page — hero, featured projects, tech stack, services, CTA, latest posts |
| `home.html` | Blog home (when using static front page) |
| `index.html` | Fallback / blog index |
| `page.html` | Generic page template |
| `single.html` | Single blog post with post meta, content, and navigation |
| `archive.html` | Category, tag, and date archives |
| `404.html` | Custom 404 error page |
| `page-projects.html` | Projects listing page |
| `page-services.html` | Services pricing page — hero, market rates, pricing table, packages, retainers, add-ons, comparison, FAQ, CTA |
| `page-blog.html` | Blog listing page |
| `page-about.html` | About page — intro, experience, tech stack, CTA |
| `single-project.html` | Single project view with meta fields, tags, and link buttons |
| `single-service.html` | Single service view with meta fields and categories |
| `archive-project.html` | Project archive listing |
| `archive-service.html` | Service archive listing |

### Custom Templates (declared in `theme.json`)

A `blank` template is also available for pages and posts that need a completely empty canvas.

---

## Block Patterns

Nine PHP-based patterns are registered under three categories:

| Category | Patterns |
|----------|----------|
| **Hero Sections** (`developer-portfolio-hero`) | `hero-section` |
| **Portfolio Sections** (`developer-portfolio-sections`) | `featured-projects`, `tech-stack`, `latest-posts`, `about-bio`, `about-stats`, `about-experience`, `about-education` |
| **Call to Action** (`developer-portfolio-cta`) | `contact-cta` |

> **Note:** The custom blocks have largely superseded these patterns. They are retained for backward compatibility and can still be inserted from the block inserter.

---

## Design System (`theme.json`)

### Color Palette (18 colors)

| Token | Hex | Usage |
|-------|-----|-------|
| `background` | `#0a0a0f` | Page background |
| `foreground` | `#e8e8ed` | Body text |
| `primary` | `#8b5cf6` | Primary violet accent |
| `secondary` | `#1a1a2e` | Card/section backgrounds |
| `accent` | `#06b6d4` | Cyan accent |
| `card` | `#12121a` | Card surface |
| `muted` | `#9696ae` | Muted/secondary text |
| `border-color` | `rgba(139,92,246,0.15)` | Subtle violet borders |
| `violet-400` | `#a78bfa` | Hover states |
| `violet-500` | `#8b5cf6` | Primary violet |
| `violet-600` | `#7c3aed` | Active/pressed states |
| `cyan-400` | `#22d3ee` | Light cyan |
| `cyan-500` | `#06b6d4` | Primary cyan |
| `cyan-600` | `#0891b2` | Dark cyan |
| `emerald-500` | `#10b981` | Success/green accent |
| `amber-500` | `#f59e0b` | Warning/amber accent |
| `white` | `#ffffff` | Button text |
| `transparent` | `transparent` | Utility |

### Gradients (5 presets)

| Name | Value |
|------|-------|
| **Violet to Cyan** | `135deg, #8b5cf6 → #06b6d4` |
| **Violet to Cyan Horizontal** | `90deg, #8b5cf6 → #06b6d4` |
| **Violet 600 to Cyan 600** | `135deg, #7c3aed → #0891b2` |
| **Violet Glow** | Subtle transparent glow effect |
| **Card Gradient** | Card background with violet/cyan edges |

### Shadows (4 presets)

| Name | Value |
|------|-------|
| Violet Small | `0 4px 14px rgba(139,92,246,0.1)` |
| Violet Medium | `0 8px 25px rgba(139,92,246,0.15)` |
| Violet Large | `0 16px 40px rgba(139,92,246,0.2)` |
| Violet XL | `0 25px 60px rgba(139,92,246,0.25)` |

### Spacing Scale (10 steps)

`0.25rem` → `0.5rem` → `0.75rem` → `1rem` → `1.5rem` → `2rem` → `3rem` → `4rem` → `6rem` → `8rem`

Units available: `%`, `px`, `em`, `rem`, `vh`, `vw`, `svh`, `dvh`

### Layout

- **Content width:** 720px
- **Wide width:** 1152px
- **Root padding aware alignments:** Enabled — full-width sections work correctly

### Custom Design Tokens

```json
{
  "fontWeight": { "light": 300, "regular": 400, "medium": 500, "semibold": 600, "bold": 700, "extrabold": 800 },
  "borderRadius": { "sm": "0.375rem", "md": "0.5rem", "lg": "0.625rem", "xl": "0.75rem", "2xl": "1rem", "3xl": "1.5rem", "full": "9999px" },
  "gap": { "baseline": "1.5rem", "horizontal": "min(1.5rem, 4vw)", "vertical": "min(4rem, 8vh)" }
}
```

---

## Typography

All fonts are **self-hosted variable WOFF2** files — no external requests to Google Fonts.

| Font | Slug | Usage | Weights |
|------|------|-------|---------|
| **Inter** | `inter` | Body text (default) | 300–900, normal + italic |
| **JetBrains Mono** | `jetbrains-mono` | Code blocks, accents, post terms, site title | 400–700 |
| **DM Sans** | `dm-sans` | Alternative sans-serif | 400–700, normal + italic |
| **Playfair Display** | `playfair-display` | Decorative serif headings | 400–900 |
| **Source Serif 4** | `source-serif-4` | Long-form serif content | 300–900, normal + italic |
| **Space Grotesk** | `space-grotesk` | Geometric sans-serif | 300–700 |
| **Fira Code** | `fira-code` | Alternative monospace | 400–700 |
| System Sans | `system-sans` | Native system sans-serif stack | — |
| System Serif | `system-serif` | Native system serif stack | — |
| System Mono | `system-mono` | Native system monospace stack | — |

### Font Size Scale (8 steps, fluid)

| Token | Size | Fluid Range |
|-------|------|-------------|
| `tiny` | 0.75rem | — |
| `small` | 0.875rem | — |
| `medium` | 1rem | — |
| `large` | 1.125rem | 1rem → 1.125rem |
| `x-large` | 1.5rem | 1.25rem → 1.5rem |
| `xx-large` | 2.5rem | 1.75rem → 2.5rem |
| `huge` | 3rem | 2rem → 3rem |
| `gigantic` | clamp(2.5rem, 7vw, 5rem) | — |

---

## JavaScript Features

`assets/js/theme.js` runs on the frontend and provides:

- **Header scroll effect** — Adds `is-scrolled` class to `.site-header` when `scrollY > 20` for glassmorphism opacity transition
- **Scroll-triggered animations** — Uses `IntersectionObserver` to animate `.project-card`, `.blog-post-card`, `.tech-card`, `.stat-card`, `.cta-card`, and `.timeline-item` elements into view
- **Hero role rotation** — Cycles through configurable role titles every 3 seconds with a fade/slide transition (reads from `data-roles` attribute or falls back to defaults)
- **Tech dot fix** — Forces correct sizing on tech stack color indicator dots overridden by WordPress layout CSS
- **Mobile nav close** — Automatically closes the responsive nav overlay when a link is clicked
- **Reduced motion support** — All animations respect `prefers-reduced-motion: reduce`
- **`js-enabled` class** — Added to `<body>` on init for progressive enhancement CSS hooks

---

## SEO & Structured Data

The theme outputs comprehensive **JSON-LD structured data** without requiring any plugins:

| Schema Type | Where | Purpose |
|-------------|-------|---------|
| `WebSite` | Every page | Site name, URL, description, search action |
| `Person` | Home & About pages | Site owner identity, skills, social links |
| `ProfilePage` | About page | E-E-A-T signal for Google |
| `SoftwareApplication` | Single project pages | Project name, description, code repository, install URL |
| `Service` | Single service pages | Service details, provider, price range, categories |
| `Article` | Single blog posts | Headline, dates, author, image |
| `BreadcrumbList` | All pages except home | Hierarchical breadcrumb navigation |
| `ItemList` | Projects & Services listings | Ordered list of items for rich results |
| `SpeakableSpecification` | Blog posts & services | Voice assistant / AEO optimization |
| `FAQPage` | Pages using `[faq]` shortcode or `services-faq` block | FAQ rich results |

### Additional SEO Features

- **Custom `robots.txt`** — Disallows wp-admin, wp-includes, plugins, cache, wp-json, and search; declares sitemaps
- **WordPress version meta tag removed** — For security and cleaner source
- **Canonical URLs** — Output on every page
- **Meta descriptions** — Auto-generated from excerpts or content
- **Image lazy loading & async decoding** — Automatically added to all content images to reduce CLS

---

## Social & Open Graph Meta

Automatically outputs on every page (no plugins needed):

- **`<meta name="description">`** — From excerpt or content
- **`<link rel="canonical">`** — Canonical URL
- **Open Graph tags** — `og:type`, `og:title`, `og:description`, `og:url`, `og:site_name`, `og:locale`, `og:image`, `og:image:alt`
- **Twitter Card tags** — `twitter:card` (summary or summary_large_image), `twitter:title`, `twitter:description`, `twitter:image`
- **Article tags** (blog posts only) — `article:published_time`, `article:modified_time`, `article:author`

---

## Security

The theme adds the following HTTP headers on frontend pages:

| Header | Value |
|--------|-------|
| `X-Content-Type-Options` | `nosniff` |
| `X-Frame-Options` | `SAMEORIGIN` |
| `Referrer-Policy` | `strict-origin-when-cross-origin` |
| `Permissions-Policy` | `camera=(), microphone=(), geolocation=(), payment=()` |

The WordPress version generator meta tag is also removed.

---

## Accessibility

- **Skip-to-content link** — Injected via `wp_body_open` for keyboard navigation (WCAG 2.4.1)
- **`aria-live` regions** — Hero role rotation announces changes to screen readers
- **`aria-label`** — Used on project link navigation, tech stack card groups
- **`prefers-reduced-motion`** — All JS animations are disabled when the user prefers reduced motion
- **Semantic HTML** — `<nav>`, `<details>/<summary>` (FAQ), `<hr>`, proper heading hierarchy
- **Screen-reader-only text** — "(opens in new tab)" labels on external links
- **Accessibility page** — Auto-created with WCAG 2.1 AA conformance statement
- **HTML5 support** — Enabled for search forms, comment forms, gallery, captions, scripts, navigation widgets

---

## Developer Filters & Hooks

Customize theme behavior from a child theme or plugin using these filters:

| Filter | Type | Description |
|--------|------|-------------|
| `developer_portfolio_owner_name` | `string` | Override the site owner display name (defaults to first admin user) |
| `developer_portfolio_owner_email` | `string` | Override the site owner email (defaults to `admin_email` option) |
| `developer_portfolio_public_email` | `string` | Set a public email for Person schema (not output by default for privacy) |
| `developer_portfolio_job_title` | `string` | Job title for Person/ProfilePage schema |
| `developer_portfolio_social_links` | `array` | Array of social profile URLs for `sameAs` schema property |
| `developer_portfolio_knows_about` | `array` | Array of skills/topics for `knowsAbout` schema property |

### Example Usage

```php
// In a child theme's functions.php or a custom plugin:
add_filter( 'developer_portfolio_job_title', function () {
    return 'Senior Full-Stack Developer';
} );

add_filter( 'developer_portfolio_social_links', function () {
    return [
        'https://github.com/your-username',
        'https://linkedin.com/in/your-profile',
        'https://twitter.com/your-handle',
    ];
} );

add_filter( 'developer_portfolio_knows_about', function () {
    return [ 'JavaScript', 'React', 'Node.js', 'WordPress', 'Cloud Architecture' ];
} );
```

---

## FAQ Shortcode

The theme includes a `[faq]` shortcode that outputs both accessible `<details>/<summary>` HTML and `FAQPage` JSON-LD schema for rich search results.

### Usage

```
[faq question="What technologies do you use?"]I primarily work with React, Node.js, WordPress, and cloud platforms like AWS.[/faq]

[faq question="Do you offer ongoing support?"]Yes, I provide maintenance and support packages for all projects.[/faq]
```

Each FAQ item renders as a collapsible `<details>` element. The collected FAQ schema is output once in `wp_footer`.

---

## Migrations

The theme includes a series of **one-time migrations** that run on `init` and self-disable via WordPress options. These ensure smooth upgrades between theme versions:

| Migration | Option Flag | Purpose |
|-----------|-------------|---------|
| 104 | `developer_portfolio_migration_104` | Delete Sample Page, create block navigation |
| 105 | `developer_portfolio_migration_105` | Backfill page content into empty existing pages |
| 107 | `developer_portfolio_migration_107` | Create Accessibility, Privacy Policy, Terms pages if missing |
| 108 | `developer_portfolio_migration_108` | Replace default WP Privacy Policy content |
| 109 | `developer_portfolio_migration_109` | Populate `tech_stack_data` meta on Home & About |
| 110 | `developer_portfolio_migration_110` | Replace static tech-stack pattern with custom block |
| 111 | `developer_portfolio_migration_111` | Replace hero/CTA patterns with custom blocks |
| 112–113 | `_migration_112`, `_113` | Clean up leftover markup on About page |
| 114 | `developer_portfolio_migration_114` | Reset About page to clean canonical content |
| 115–116 | `_migration_115`, `_116` | Migrate about-bio/stats → about-intro block; experience/education → about-experience block |
| 117 | `developer_portfolio_migration_117` | Migrate Home page to use featured-projects & latest-posts blocks |
| 118 | `developer_portfolio_migration_118` | Reorder Home page blocks (services above CTA) |
| 119 | `developer_portfolio_migration_119` | Replace Blog, Projects, Services pages with listing blocks |
| 120 | `developer_portfolio_migration_120` | Create 6 service categories and 27 service posts with full pricing data |
| 121 | `developer_portfolio_migration_121` | Update Services page content with 9 new services pricing blocks |
| 122 | `developer_portfolio_migration_122` | Populate all service posts with bound block content for inline editor preview |
| 123 | `developer_portfolio_migration_123` | Rebuild service post content with excerpt text in description paragraph |
| 124 | `developer_portfolio_migration_124` | Remove raw URL bound paragraphs from project post content (sidebar handles URLs) |
| 125 | `developer_portfolio_migration_125` | Populate 11 project posts from live site with categories, tags, meta, and full block content |
| 126 | `developer_portfolio_migration_126` | Update Projects page to two category-filtered sections (all projects + proprietary) |
| 127 | `developer_portfolio_migration_127` | Fix Projects page content encoding (unicode escape fix) |
| 128 | `developer_portfolio_migration_128` | Remove default description from proprietary section |
| 129 | `developer_portfolio_migration_129` | Populate Home page tech stack data to match live site |

---

## Customization Guide

### Using the Site Editor

All design tokens are defined in `theme.json` and can be changed visually via **Appearance → Editor → Styles**:

- **Colors** — Swap any palette color from Global Styles
- **Fonts** — Choose from 10 bundled font families (or add your own)
- **Spacing** — Adjust the 10-step spacing scale
- **Templates** — Edit any of the 15 templates directly in the visual editor
- **Template parts** — Customize the header and footer
- **Blocks** — Rearrange, add, or remove custom blocks on any page

### Editing Block Content

Every custom block exposes its content via the block editor sidebar (InspectorControls). For example:

- **Hero Section** — Change name, role text, description, button labels/URLs, and the list of rotating roles
- **Featured Projects** — Pick specific projects or set a count to show
- **Tech Stack** — Open the "Tech Stack" sidebar panel to add/remove categories and badges with color selection
- **Site Header** — Edit navigation items, CTA button text/URL, upload logo/favicon
- **Social Links** — Update GitHub, LinkedIn, X, and email URLs

### Dynamic Year

The footer uses `{year}` as a placeholder, which is automatically replaced with the current year at render time.

### Proprietary Projects

If a project has no GitHub URL and no live URL, the link buttons are automatically replaced with a "(Proprietary software)" label.

### Adding New Fonts

1. Place `.woff2` files in `assets/fonts/`
2. Add a `fontFace` entry to `theme.json` under `settings.typography.fontFamilies`
3. The font becomes available in the editor's font picker immediately

---

## License

**GNU General Public License v2 or later**
[http://www.gnu.org/licenses/gpl-2.0.html](http://www.gnu.org/licenses/gpl-2.0.html)

Built by **Jeremy Anderson** — [jeremyanderson.tech](https://jeremyanderson.tech) | [GitHub](https://github.com/CptNope)
