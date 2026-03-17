# Mattys Catering WordPress Theme - Expert Guide

## Project Overview

This is a custom WordPress theme for **Mattys Sandwiches**, an online catering ordering system. Built on the Underscores (_s) starter theme, it enables customers to browse menu items, customize products, manage a shopping cart, and submit catering orders via email.

**Theme Version:** 1.0.2
**WordPress Compatibility:** 5.4+
**PHP Requirement:** 5.6+

---

## Directory Structure

```
mattys/
├── inc/                          # Core PHP functionality
│   ├── init.php                 # Theme setup, menus, widgets
│   ├── enqueue.php              # Scripts & styles registration
│   ├── custom-header.php        # Header customization & favicon
│   ├── template-tags.php        # Reusable template functions
│   ├── template-functions.php   # Body classes, pingback headers
│   ├── customizer.php           # WordPress customizer settings
│   ├── cpt-orders.php           # Orders custom post type & admin UI
│   ├── emailorder.php           # AJAX order email handler & save
│   ├── jetpack.php              # Jetpack plugin support
│   └── shortcodes/
│       └── productlisting.php   # Product grid shortcode & modal AJAX
├── assets/
│   ├── css/
│   │   ├── styles.css          # Additional custom styles
│   │   └── simplebar.css       # Custom scrollbar styling
│   ├── js/
│   │   ├── main.js             # Core application logic (789 lines)
│   │   ├── navigation.js        # Mobile menu toggle
│   │   ├── customizer.js        # Theme customizer preview
│   │   ├── jquery.validate.min.js
│   │   ├── additional-methods.min.js
│   │   └── simplebar.min.js
│   └── images/                  # UI assets & favicons
├── template-parts/
│   ├── content.php             # Default post content
│   ├── content-page.php        # Page content
│   ├── content-cart.php        # Main catering order form
│   ├── content-search.php      # Search results
│   └── content-none.php        # No posts found
├── header.php                   # Site header
├── footer.php                   # Site footer with modals
├── cartform.php                 # Custom template for cart page
├── style.css                    # Main stylesheet (56KB)
├── functions.php               # Theme bootstrap
├── package.json                # npm configuration
└── composer.json               # PHP dependencies
```

---

## Key Files Reference

| File | Purpose | Lines |
|------|---------|-------|
| `inc/shortcodes/productlisting.php` | Product shortcode & AJAX modal | 545 |
| `assets/js/main.js` | Cart, validation, order submission | 789 |
| `inc/cpt-orders.php` | Orders CPT, meta boxes, admin UI | ~450 |
| `inc/emailorder.php` | Order email processing & save | ~270 |
| `template-parts/content-cart.php` | Main order form template | ~200 |
| `style.css` | Primary styles + theme metadata | 2000+ |

---

## Custom Post Types & Taxonomy

### Menu Products (Plugin-provided)
- **Post Type:** `menuproduct` - Menu items (sandwiches, drinks)
- **Taxonomy:** `menuproduct_category` - Product categories

### Orders (Theme-provided)
- **Post Type:** `mattys_order` - Customer catering orders
- **File:** `inc/cpt-orders.php`
- **Custom Statuses:** `order-pending`, `order-confirmed`, `order-completed`, `order-cancelled`
- **Order Number Format:** `MTY` + `YYYYMMDD` + `XXX` (e.g., MTY20260315001)

---

## ACF (Advanced Custom Fields) Integration

Products use these ACF fields:

| Field Name | Type | Purpose |
|------------|------|---------|
| `choose_portion_size` | Repeater | Array with size/price options |
| `choose_cut_preference` | Repeater | Cut options (Medium, Thick, etc.) |
| `modify_no_sandwich` | Text | Simple removal options |
| `modify_no_sandwich_list` | Repeater | Items that can be removed |
| `modify_addon_sandwich` | Text | Simple add-on options |
| `modify_addon_sandwich_list` | Repeater | Add-ons with prices |
| `add_on_drinks` | Boolean | Indicates drink add-ons available |
| `popular` | Boolean | Shows "popular" badge |

### Order Meta Fields (mattys_order)

| Meta Key | Type | Purpose |
|----------|------|---------|
| `_order_number` | String | Unique order identifier (MTY format) |
| `_order_date` | DateTime | Order submission timestamp |
| `_customer_fullname` | String | Customer full name |
| `_customer_phone` | String | Customer phone number |
| `_customer_email` | String | Customer email address |
| `_customer_company` | String | Company/organization name |
| `_total_people` | String | Number of people catering for |
| `_delivery_date` | String | Requested delivery date |
| `_delivery_time` | String | Requested delivery time |
| `_pickup_delivery` | String | "pickup" or "delivery" |
| `_delivery_address` | String | Delivery address (if applicable) |
| `_dietary_requirements` | Text | Dietary requirements notes |
| `_special_request` | Text | Special request notes |
| `_order_total` | String | Order total amount |
| `_order_items` | JSON | Cart items array (JSON encoded) |

---

## Shortcodes

### `[productlisting]`

Displays product grid with add-to-cart functionality.

**Parameters:**
- `catid` - Category ID(s), comma-separated
- `title` - Section title
- `template` - Display template (1=grid, 2=inline buttons, 3=mobile thumbnails)

**Example:**
```
[productlisting catid="5,6" title="Sandwiches" template="1"]
```

---

## AJAX Endpoints

### 1. `productmenumodal`
- **File:** `inc/shortcodes/productlisting.php:249`
- **Action:** Loads product details into Bootstrap modal
- **Input:** productId, catid, catname
- **Access:** Both logged-in and guest users

### 2. `mattysorderemail`
- **File:** `inc/emailorder.php`
- **Action:** Processes order submission, sends confirmation emails
- **Security:** Nonce verification required (`mattys-nonce`)
- **Input:** Form data + cart items (JSON from localStorage)

---

## JavaScript Architecture (main.js)

### Core Systems

1. **Cart Management** (localStorage-based)
   - `addToCart()` - Add/update items
   - `removeFromCart()` - Remove items
   - `updateCartDisplay()` - Refresh UI
   - `calculateTotal()` - Sum cart items

2. **Product Modal**
   - AJAX loads product form
   - Handles option selection
   - Quantity increment/decrement

3. **Form Validation**
   - jQuery Validate integration
   - $200 minimum order enforcement
   - Required field validation

4. **Delivery Time Logic**
   - 30-minute intervals
   - 3-hour lead time enforcement
   - Operating hours: 8:00 AM - 4:00 PM
   - Auto-adjusts to next available slot

5. **Order Submission**
   - Sends cart + form data via AJAX
   - Clears cart on success
   - Displays success/error messages

---

## Styling

### Colors
- **Primary:** `#013F42` (teal)
- **Text:** `#000000`

### Typography
- **Font:** "Roboto Mono" (monospace)

### Framework
- Bootstrap 5.3.2 (CDN)
- Custom component styles in style.css

---

## Dependencies

### npm (package.json)
```json
"@wordpress/scripts": "^19.2.2"
"node-sass": "^7.0.1"
"rtlcss": "^3.5.0"
```

### CDN
- Bootstrap 5.3.2 (CSS + JS)
- Popper.js 2.11.8
- Google Fonts (Roboto Mono)

---

## Build Commands

```bash
# Development
npm run watch           # Watch SASS files
npm run compile:css    # Compile SASS + lint
npm run compile:rtl    # Generate RTL stylesheet

# Linting
npm run lint:scss      # Lint SASS
npm run lint:js        # Lint JavaScript
composer lint:wpcs     # PHP CodeSniffer
composer lint:php      # PHP syntax check

# Production
npm run bundle         # Create distribution .zip
composer make-pot      # Generate translation file
```

---

## Data Flow

```
1. User browses products → [productlisting] shortcode renders grid
2. Click "ADD" → AJAX loads productmenumodal into Bootstrap modal
3. Select options, quantity → Click "ADD ITEM"
4. JavaScript saves to localStorage → Cart sidebar updates
5. Fill order form → Click "SUBMIT ENQUIRY"
6. AJAX sends to mattysorderemail → Server validates
7. Order saved to mattys_order CPT → Order number generated
8. Confirmation emails sent to admin + customer
9. Success → Cart cleared, confirmation shown
```

---

## Security Implementation

- **Nonce verification:** `wp_verify_nonce('mattys-nonce')` on all AJAX
- **Input sanitization:** `sanitize_text_field()`, `sanitize_email()`, `sanitize_textarea_field()`
- **Output escaping:** `esc_html()`, `esc_attr()`, `esc_url()`

---

## Common Development Tasks

### Add New Product Field
1. Create ACF field in WordPress admin
2. Update `inc/shortcodes/productlisting.php` modal output
3. Update `assets/js/main.js` cart handling
4. Update `inc/emailorder.php` email template

### Modify Order Form
1. Edit `template-parts/content-cart.php`
2. Update validation in `assets/js/main.js`
3. Update email handler in `inc/emailorder.php`

### Change Minimum Order
1. Search for `200` in `assets/js/main.js`
2. Update validation message and condition

### Modify Operating Hours
1. Edit time generation logic in `assets/js/main.js`
2. Look for `timeValue` and hour comparisons

### Add New Product Category
1. Create category under `menuproduct_category` taxonomy
2. Use category ID in `[productlisting catid="X"]` shortcode

---

## Template Hierarchy

| Template | Purpose |
|----------|---------|
| `index.php` | Default/fallback |
| `page.php` | Static pages |
| `single.php` | Single posts |
| `archive.php` | Archives |
| `search.php` | Search results |
| `404.php` | Not found |
| `cartform.php` | Cart/order page (custom template) |

---

## Widget Areas

- `sidebar-1` - General sidebar widget area

---

## Theme Features Enabled

- Post thumbnails
- Custom logo (250x250px, flexible)
- Navigation menus (1 primary)
- HTML5 markup
- Custom backgrounds
- Custom headers
- RTL support
- Jetpack (infinite scroll, responsive videos)

---

## File Modification Quick Reference

| Task | Files to Edit |
|------|---------------|
| Product display | `inc/shortcodes/productlisting.php` |
| Cart functionality | `assets/js/main.js` |
| Order form | `template-parts/content-cart.php` |
| Email content | `inc/emailorder.php` |
| Order CPT & admin | `inc/cpt-orders.php` |
| Order meta fields | `inc/cpt-orders.php`, `inc/emailorder.php` |
| Styles | `style.css`, `assets/css/styles.css` |
| Scripts loading | `inc/enqueue.php` |
| Theme setup | `inc/init.php` |

---

## Session Tracking

This project uses a session tracking system for continuity between Claude Code sessions.

### Files

| File | Purpose |
|------|---------|
| `SESSION_LOG.md` | Detailed session history with what was done |
| `TASKS.md` | In-progress and pending task tracker |
| `CLAUDE.md` | This file - includes recent sessions summary below |

### Workflow

**Starting a session:**
1. Claude reads `SESSION_LOG.md` for recent context
2. Claude checks `TASKS.md` for pending work
3. Claude reviews "Recent Sessions" below

**Ending a session:**
Ask Claude: "Update the session files before we end"

---

## Recent Sessions

| Date | Focus | Key Changes |
|------|-------|-------------|
| 2026-03-16 | Project setup | Created CLAUDE.md, session tracking system |

_Last 5 sessions shown. See SESSION_LOG.md for full history._
